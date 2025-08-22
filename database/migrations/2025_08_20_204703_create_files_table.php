<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('conversation_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('file_type'); // pdf, docx, txt, csv, image, other
            $table->string('mime_type');
            $table->string('file_path');
            $table->bigInteger('file_size');
            $table->json('metadata')->nullable();
            $table->boolean('is_processed')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'conversation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};