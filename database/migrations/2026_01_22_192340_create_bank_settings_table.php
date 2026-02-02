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
        Schema::create('bank_settings', function (Blueprint $table) {
            $table->id();
            $table->string('bank_id');
            $table->string('account_no')->nullable();
            $table->string('owner_name')->nullable();
            $table->integer('is_active')->default(1);
            $table->double('capital')->default(0);
            $table->double('amount')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_settings');
    }
};
