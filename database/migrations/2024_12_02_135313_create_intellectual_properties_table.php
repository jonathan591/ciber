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
        Schema::create('intellectual_properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('author');
            $table->foreignId('category_id')->constrained('categories');
            $table->text('description');
            $table->foreignId('owner_id')->constrained('users');
         
            $table->string('file');
            $table->string('file_hash');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('intellectual_properties');
    }
};
