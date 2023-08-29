<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Winata\Core\Telegram\Models\TelegramBot;
use Winata\Core\Telegram\Models\TelegramChat;
use Winata\Core\Telegram\Models\TelegramLog;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('exceptions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId(config('winata.response.performer.column'))
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade');
            $table->string('performer')->default('system');
            $table->string('ip');

            $table->string('url')->nullable();
            $table->string('rc')->nullable();
            $table->text('data')->nullable();
            $table->text('message');
            $table->string('source');
            $table->integer('code');
            $table->string('file');
            $table->integer('line');
            $table->text('trace')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_logs');
    }
};
