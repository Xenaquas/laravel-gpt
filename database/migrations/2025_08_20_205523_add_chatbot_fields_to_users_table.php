<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar_url')->nullable()->after('email');
            $table->string('role')->default('user')->after('avatar_url');
            $table->json('preferences')->nullable()->after('role');
            $table->bigInteger('total_tokens_used')->default(0)->after('preferences');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar_url', 'role', 'preferences', 'total_tokens_used']);
        });
    }
};