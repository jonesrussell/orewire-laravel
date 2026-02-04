<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mining_article_mining_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mining_category_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mining_article_id', 'mining_category_id'], 'mining_art_cat_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mining_article_mining_category');
    }
};
