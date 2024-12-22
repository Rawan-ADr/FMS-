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
          Schema::create('files', function (Blueprint $table) {
            $table->id();
               $table->string('name')->unique(); 
               $table->string('path'); 
               $table->string('type'); 
               $table->unsignedBigInteger('size'); 
               $table->enum('state', ['reserved', 'free']); 
               $table->string('user_id')->nullable();
               $table->foreignId('user_group_id')->constrained('user_groups')->cascadeOnDelete()->cascadeOnUpdate()->unique();
             $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
