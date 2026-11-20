<?php

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
        Schema::create('conversion_histories', function (Blueprint $table) {
            $table->id();
            $table->string('session_key', 100);
            $table->decimal('amount', 20, 8);
            $table->string('source_currency', 3);
            $table->string('target_currency', 3);
            $table->decimal('rate', 20, 10);
            $table->decimal('converted_amount', 20, 8);
            $table->date('rate_date');
            $table->timestamps();

            $table->index(['session_key', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversion_histories');
    }
};
