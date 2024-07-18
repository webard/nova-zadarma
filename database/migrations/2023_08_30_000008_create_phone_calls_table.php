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

            $table->phone('caller_phone_number', 20)->comment('Phone number in E.164 format.');
            $table->phone('receiver_phone_number')->comment('Phone number in E.164 format.');

            $table->boolean('is_answered')->default(false);

            /**
             * 'answered' – rozmowa,
             * 'busy' – zajęte,
             * 'cancel' - odrzucone,
             * 'no answer' - brak odpowiedzi,
             * 'failed' - nieudane,
             * 'no money' - brak środków, przekroczony limit,
             * 'unallocated number' - numer nie istnieje,
             * 'no limit' - przekroczony limit,
             * 'no day limit' - przekroczony dzienny limit,
             * 'line limit' - przekroczony limit linii,
             * 'no money, no limit' - przekroczony limit
             */
            $table->string('disposition')->default('pending');
            $table->string('itu_standard_code')->default(null)->comment('ITU-T Recommendation Q.931.');

            $table->unsignedInteger('duration')->nullable();

            $table->string('type');

            $table->string('recording')->nullable();

            $table->timestamps();
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
