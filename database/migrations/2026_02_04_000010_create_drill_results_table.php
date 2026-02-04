<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drill_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mining_article_id')->constrained()->cascadeOnDelete();
            $table->foreignId('commodity_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('hole_id')->nullable();
            $table->decimal('intercept_m', 10, 2)->nullable();
            $table->decimal('grade', 10, 4)->nullable();
            $table->string('unit', 20)->nullable();
            $table->timestamps();

            $table->index('mining_article_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drill_results');
    }
};
