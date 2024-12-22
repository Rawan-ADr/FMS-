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
           Schema::create('file_copies', function (Blueprint $table) {
            $table->id();
              $table->string('name'); 
               $table->string('path');
               $table->string('type');
               $table->unsignedBigInteger('size'); 
               $table->foreignId('file_id')->constrained('files')->cascadeOnDelete()->cascadeOnUpdate()->unique();
               $table->Integer('copyNum');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_copies');
    }
};
