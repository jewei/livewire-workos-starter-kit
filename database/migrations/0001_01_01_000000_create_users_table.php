<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table): void {
            $table->id();
            $table->uuid()->unique();
            $table->string('workos_id')->unique();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('avatar', 500)->nullable();
            $table->string('authentication_method')->nullable();
            $table->string('locale', 5)->default(config('app.locale', 'en'));
            $table->string('timezone', 32)->default(config('app.timezone', 'UTC'));
            $table->string('referral_code', 20)->unique();
            $table->string('referred_code', 20)->nullable()->index();
            $table->unsignedBigInteger('referred_by')->nullable()->index();
            $table->string('source', 20)->default(config('app.name'));
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table): void {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('sessions');
    }
};
