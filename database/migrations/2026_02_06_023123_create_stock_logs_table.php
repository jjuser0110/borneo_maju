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
        Schema::create('stock_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('bank_setting_id')->nullable();
            $table->integer('stock_id')->nullable();
            $table->integer('order_id')->nullable();
            $table->double('idr_amount')->nullable();
            $table->double('stock_idr_rate')->nullable();
            $table->double('capital_used')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_logs');
    }
};
