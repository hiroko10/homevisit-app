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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('last_name')->comment('姓');
            $table->string('first_name')->comment('名');
            $table->string('last_name_kana')->comment('姓（ふりがな）');
            $table->string('first_name_kana')->comment('名（ふりがな）');
            $table->text('memo')->nullable();
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
