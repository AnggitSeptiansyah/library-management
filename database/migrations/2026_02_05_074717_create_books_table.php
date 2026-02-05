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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('isbn')->unique();
            $table->string('author');
            $table->string('publisher');
            $table->foreignId('category_id')->constrained()->onDelete('restrict');
            $table->string('edition')->nullable();
            $table->year('pubblisher_year')->nullable();
            $table->integer('total_pages')->nullable();
            $table->integer('available_copies')->default(1);
            $table->text('description');
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
