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
        Schema::create('account_customer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained()->onDelete('CASCADE');
            $table->foreignId('customer_id')->constrained()->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('account_customer');
    }
};
