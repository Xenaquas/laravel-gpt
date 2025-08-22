<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->constrained()->onDelete('cascade');
            $table->text('chunk_text');
            $table->integer('chunk_index');
            $table->json('embedding_vector');
            $table->string('embedding_model')->default('nomic-embed-text');
            $table->timestamps();
            
            $table->index(['file_id', 'chunk_index']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};