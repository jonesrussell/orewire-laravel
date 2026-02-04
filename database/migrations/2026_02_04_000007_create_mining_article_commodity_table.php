<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mining_article_commodity', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commodity_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['mining_article_id', 'commodity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mining_article_commodity');
    }
};
