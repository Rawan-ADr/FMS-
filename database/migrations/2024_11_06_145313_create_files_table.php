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
               $table->string('name'); 
               $table->text('content'); 
               $table->string('type'); 
               $table->enum('state', ['reserved', 'free']); 
               $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate()->unique();
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
