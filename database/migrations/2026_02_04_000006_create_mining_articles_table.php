<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mining_articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('news_source_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mining_jurisdiction_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->text('excerpt')->nullable();
            $table->longText('content')->nullable();
            $table->string('url');
            $table->string('external_id');
            $table->string('slug')->unique();
            $table->string('image_url')->nullable();
            $table->string('author')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('crawled_at')->nullable();
            $table->json('metadata')->nullable();
            $table->integer('view_count')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('updated_via_ingest_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->unique(['news_source_id', 'external_id']);
            $table->index('published_at');
            $table->index('news_source_id');
            $table->index('mining_jurisdiction_id');
            $table->index('is_featured');
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('mining_articles', function (Blueprint $table) {
                $table->fullText(['title', 'excerpt', 'content']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('mining_articles');
    }
};
