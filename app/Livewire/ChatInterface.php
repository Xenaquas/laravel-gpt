<?php

namespace App\Livewire;

use App\Events\NewTokenReceived;
use App\Models\Conversation;
use App\Models\Message;
use Livewire\Component;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class ChatInterface extends Component
{
    public $conversations;
    public $currentConversation;
    public $messageInput = '';
    public $selectedModel = 'mistral';
    public $isStreaming = false;

    public ?Message $streamingMessage = null;
    public string $fullStreamedResponse = '';

    public $availableModels = [
        'mistral' => 'Mistral',
        'tinyllama' => 'Llama',
        'gemma3:4b' => 'Gemma 3',
        'codellama' => 'Code Llama'
    ];

    public function mount()
    {
        $this->loadConversations();
        $this->selectedModel = config('app.default_model', 'mistral');
    }

    public function render()
    {
        return view('livewire.chat-interface');
    }

    public function loadConversations()
    {
        $this->conversations = auth()->user()
            ->conversations()
            ->latest()
            ->get();
    }

    public function createNewConversation()
    {
        $this->currentConversation = auth()->user()->conversations()->create([
            'title' => 'New Conversation',
        ]);
        
        $this->loadConversations();
    }

    public function selectConversation($conversationId)
    {
        $this->currentConversation = Conversation::with('messages')
            ->where('user_id', auth()->id())
            ->findOrFail($conversationId);
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageInput))) {
            return;
        }

        if (!$this->currentConversation) {
            $this->createNewConversation();
        }

        // Save user message
        $this->currentConversation->messages()->create([
            'role' => 'user',
            'content' => $this->messageInput
        ]);

        $prompt = $this->messageInput;
        $this->messageInput = '';

        // Create a placeholder for the assistant's response
        $this->streamingMessage = $this->currentConversation->messages()->create([
            'role' => 'assistant',
            'content' => '', // Start with empty content
        ]);

        $this->isStreaming = true;
        $this->fullStreamedResponse = '';
        $this->dispatch('stream-started', messageId: $this->streamingMessage->id);

        // Generate AI response
        $this->generateAIResponse($prompt);
    }

    protected function generateAIResponse($prompt)
    {
        try {
            // THE FIX IS HERE: Changed ->stream() to ->asStream()
            $stream = Prism::text()
                ->using(Provider::Ollama, $this->selectedModel)
                ->withSystemPrompt($this->getSystemPrompt())
                ->withPrompt($prompt)
                ->asStream();

            foreach ($stream as $response) {
                $chunk = $response->text;
                $this->fullStreamedResponse .= $chunk;
                // Broadcast each chunk
                broadcast(new NewTokenReceived($this->streamingMessage->id, $chunk));
            }

            if ($this->streamingMessage) {
                $this->streamingMessage->update(['content' => $this->fullStreamedResponse]);
            }

        } catch (\Exception $e) {
            if ($this->streamingMessage) {
                $this->streamingMessage->update(['content' => 'Error: Could not connect to the AI model. Details: ' . $e->getMessage()]);
            }
        } finally {
            $this->isStreaming = false;
            $this->streamingMessage = null;
            $this->fullStreamedResponse = '';
            $this->dispatch('stream-finished');
            $this->currentConversation->refresh();
        }
    }

    protected function getSystemPrompt()
    {
        return "You are a helpful AI assistant. Provide clear, concise, and accurate responses. Format your responses using Markdown when appropriate.";
    }

    public function deleteConversation($conversationId)
    {
        $conversation = Conversation::where('user_id', auth()->id())
            ->findOrFail($conversationId);
        
        $conversation->delete();
        
        if ($this->currentConversation && $this->currentConversation->id === $conversationId) {
            $this->currentConversation = null;
        }
        
        $this->loadConversations();
    }
}