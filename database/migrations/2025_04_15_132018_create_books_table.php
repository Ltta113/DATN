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
            $table->timestamps();
            $table->string('title');
            $table->string('description')->nullable();
            $table->string('slug')->unique();
            $table->string('isbn')->unique()->nullable();
            $table->date('published_at')->nullable();
            $table->foreignId('publisher_id')->nullable()->constrained('publishers')->onDelete('cascade');
            $table->string('cover_image')->nullable();
            $table->decimal('price', 15, 2)->nullable();
            $table->integer('stock')->default(0);
            $table->string('language')->nullable()->default('vi');
            $table->integer('page_count')->nullable();
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
