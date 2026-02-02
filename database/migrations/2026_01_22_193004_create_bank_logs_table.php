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
        Schema::create('bank_logs', function (Blueprint $table) {
            $table->id();
            $table->integer('content_id')->nullable();
            $table->string('content_type')->nullable();
            $table->integer('bank_setting_id')->default(0);
            $table->string('type')->nullable();
            $table->string('remarks')->nullable();
            $table->double('prev_amount')->nullable();
            $table->double('amount')->nullable();
            $table->double('after_amount')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_logs');
    }
};
