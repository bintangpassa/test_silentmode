<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name')->nullable();
            $table->string('status', 20)->default('offline')->index();
            $table->string('ip', 45)->nullable();
            $table->text('note')->nullable();
            $table->timestamp('first_connected')->nullable();
            $table->timestamp('last_connected')->nullable();
            $table->timestamp('last_download')->nullable();
            $table->timestamp('timestamp')->nullable();
            $table->string('file_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
};
