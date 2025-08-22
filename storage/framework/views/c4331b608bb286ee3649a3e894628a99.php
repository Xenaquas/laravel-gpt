<div
    
    x-data="{
        isStreaming: <?php if ((object) ('isStreaming') instanceof \Livewire\WireDirective) : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isStreaming'->value()); ?>')<?php echo e('isStreaming'->hasModifier('live') ? '.live' : ''); ?><?php else : ?>window.Livewire.find('<?php echo e($__livewire->getId()); ?>').entangle('<?php echo e('isStreaming'); ?>')<?php endif; ?>,
        streamingMessageId: null,

        init() {
            // Automatically scroll to the bottom when the page loads or updates
            this.scrollToBottom();
            Livewire.hook('morph.updated', () => this.scrollToBottom());

            // Listen for events dispatched from the Livewire component
            Livewire.on('stream-started', (event) => {
                this.streamingMessageId = event.messageId;
                this.connectToStream();
            });

            Livewire.on('stream-finished', () => {
                this.disconnectFromStream();
                // Re-apply syntax highlighting after stream is complete
                this.$nextTick(() => {
                    document.querySelectorAll('pre code').forEach((block) => {
                        hljs.highlightElement(block);
                    });
                });
            });
        },

        connectToStream() {
            if (this.echoChannel) {
                window.Echo.leave(this.echoChannel);
            }

            this.echoChannel = `chat.${this.streamingMessageId}`;

            this.$nextTick(() => {
                let streamContainer = document.getElementById(`stream-container-${this.streamingMessageId}`);
                if (streamContainer) {
                    streamContainer.innerHTML = ''; // Clear previous content
                    window.Echo.private(this.echoChannel)
                        .listen('.new-token', (e) => {
                            streamContainer.innerHTML += e.chunk;
                            this.scrollToBottom();
                        });
                }
            });
        },

        disconnectFromStream() {
            if (this.echoChannel) {
                window.Echo.leave(this.echoChannel);
                this.echoChannel = null;
            }
        },

        scrollToBottom() {
            const container = document.getElementById('messages-container');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        }
    }"
    class="flex h-screen bg-gray-50"
>
    
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col flex-shrink-0">
        
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-semibold text-gray-800">Chats</h1>
                <button wire:click="createNewConversation"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1.5 rounded-lg text-sm font-semibold transition-colors">
                    New Chat
                </button>
            </div>

            
            <select wire:model.live="selectedModel"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $availableModels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($key); ?>"><?php echo e($name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </select>
        </div>

        
        <div class="flex-1 overflow-y-auto">
            <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $conversations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $conversation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div wire:click="selectConversation(<?php echo e($conversation->id); ?>)"
                     class="p-3 hover:bg-gray-50 cursor-pointer border-b border-gray-100 <?php echo e($currentConversation && $currentConversation->id === $conversation->id ? 'bg-blue-50 border-l-4 border-blue-500' : ''); ?>">
                    <div class="flex justify-between items-start">
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 truncate">
                                <?php echo e($conversation->title ?: 'New Conversation'); ?>

                            </h3>
                            <p class="text-xs text-gray-500 mt-1">
                                <?php echo e($conversation->created_at->format('M j, Y')); ?>

                            </p>
                        </div>
                        <button wire:click.stop="deleteConversation(<?php echo e($conversation->id); ?>)"
                                class="text-gray-400 hover:text-red-600 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="p-4 text-center text-gray-500 text-sm">
                    No conversations yet.
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        
        <div class="p-4 border-t border-gray-200">
             <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                        <span class="text-white text-sm font-medium">
                            <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                        </span>
                    </div>
                    <p class="text-sm font-medium text-gray-900 truncate">
                        <?php echo e(auth()->user()->name); ?>

                    </p>
                </div>
                <form method="POST" action="<?php echo e(route('logout')); ?>">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="text-gray-500 hover:text-red-600 text-sm font-semibold">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    
    <div class="flex-1 flex flex-col">
        <!--[if BLOCK]><![endif]--><?php if($currentConversation): ?>
            
            <div class="flex-1 overflow-y-auto p-6 space-y-6" id="messages-container">
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $currentConversation->messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex <?php echo e($message->role === 'user' ? 'justify-end' : 'justify-start'); ?>">
                        <div class="max-w-3xl prose prose-sm <?php echo e($message->role === 'user' ? 'bg-blue-600 text-white prose-invert' : 'bg-white border border-gray-200 text-gray-800'); ?> rounded-lg p-4 shadow-sm">
                            <?php echo \Illuminate\Support\Str::markdown($message->content ?? ''); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                
                <div x-show="isStreaming" class="flex justify-start" style="display: none;">
                    <div class="max-w-3xl prose prose-sm bg-white border border-gray-200 text-gray-800 rounded-lg p-4 shadow-sm">
                        <div x-bind:id="`stream-container-${streamingMessageId}`"></div>
                    </div>
                </div>
            </div>

            
            <div x-show="isStreaming" x-transition class="px-6 pb-2 text-center" style="display: none;">
                <button wire:click="stopStreaming" class="bg-white border border-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-50">
                    Stop Generating
                </button>
            </div>

            
            <div class="border-t border-gray-200 p-6 bg-white">
                <form wire:submit.prevent="sendMessage" class="space-y-4">
                    <div class="flex items-end space-x-4">
                        <div class="flex-1">
                            <textarea wire:model="messageInput"
                                      placeholder="Type your message..."
                                      rows="1"
                                      @keydown.enter.prevent.stop="if (!$event.shiftKey) { $wire.sendMessage() }"
                                      x-data="{
                                        resize() {
                                            $el.style.height = 'auto';
                                            $el.style.height = $el.scrollHeight + 'px';
                                        }
                                      }"
                                      x-init="resize()"
                                      @input="resize()"
                                      class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none max-h-48"
                                      :disabled="$wire.isStreaming"></textarea>
                        </div>

                        <button type="submit"
                                wire:loading.attr="disabled"
                                :disabled="$wire.isStreaming"
                                class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white p-3 rounded-lg transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </div>

                    <p class="text-xs text-gray-500">
                        Shift+Enter for a new line • Using <?php echo e($availableModels[$selectedModel] ?? 'Default'); ?>

                    </p>
                </form>
            </div>
        <?php else: ?>
            
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center max-w-md">
                    <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456L3 21l2.544-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">AI Chat</h2>
                    <p class="text-gray-600 mb-6">Start a new chat to begin your conversation.</p>
                    <button wire:click="createNewConversation"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors font-semibold">
                        Start New Chat
                    </button>
                </div>
            </div>
        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
    </div>
</div><?php /**PATH A:\xampp\htdocs\laravel-gpt\resources\views/livewire/chat-interface.blade.php ENDPATH**/ ?>