<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('phone_calls', function (Blueprint $table): void {
            $table->id();

            $table->string('pbx_call_id')
                ->nullable()
                ->unique();

            $table->string('caller_phone_number', 20)
                ->comment('Phone number in E.164 format.')
                ->nullable();

            $table->unsignedSmallInteger('caller_sip')
                ->nullable();

            $table->foreignId('caller_id')
                ->nullable()
                ->constrained('users');

            $table->string('receiver_phone_number', 20)
                ->comment('Phone number in E.164 format.')
                ->nullable();

            $table->unsignedSmallInteger('receiver_sip')
                ->nullable();

            $table->foreignId('receiver_id')
                ->nullable()
                ->constrained('users');

            $table->boolean('is_answered')
                ->default(false);

            $table->string('disposition')
                ->default('pending');

            $table->unsignedInteger('duration')
                ->nullable();

            $table->string('type');

            $table->string('recording')
                ->nullable();

            $table->timestamps();

            $table->timestamp('started_at')
                ->nullable();

            $table->timestamp('ended_at')
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phone_calls');
    }
};
