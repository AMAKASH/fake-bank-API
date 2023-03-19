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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('customers', 'id')->onDelete('Restrict')->nullable();
            $table->foreignId('recipient_id')->constrained('customers', 'id')->onDelete('Restrict')->nullable();
            $table->double('amount');
            $table->string('status', 10);
            $table->foreignId('employee_id')->constrained('users', 'id');
            $table->text('details');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
