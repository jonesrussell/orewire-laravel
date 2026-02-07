<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mining_articles', function (Blueprint $table) {
            $table->string('status')->default('published')->index()->after('author');
        });
    }

    public function down(): void
    {
        Schema::table('mining_articles', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
