<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Switches search_vector from 'english' to 'simple' config so contractions
     * like "don't" produce indexable lexemes (e.g. "don") that match prefix
     * searches. The 'english' config filters "don" as a stop word.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS posts_search_vector_idx');
        DB::statement('ALTER TABLE posts DROP COLUMN IF EXISTS search_vector');
        DB::statement("
            ALTER TABLE posts
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('simple', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('simple', coalesce(content, '')), 'B')
            ) STORED
        ");
        DB::statement('CREATE INDEX posts_search_vector_idx ON posts USING GIN (search_vector)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            return;
        }

        DB::statement('DROP INDEX IF EXISTS posts_search_vector_idx');
        DB::statement('ALTER TABLE posts DROP COLUMN IF EXISTS search_vector');
        DB::statement("
            ALTER TABLE posts
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('english', coalesce(title, '')), 'A') ||
                setweight(to_tsvector('english', coalesce(content, '')), 'B')
            ) STORED
        ");
        DB::statement('CREATE INDEX posts_search_vector_idx ON posts USING GIN (search_vector)');
    }
};
