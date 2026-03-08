<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Strips ASCII and Unicode apostrophes (U+2019, U+02BC) before indexing so
     * "don't" and "dont" produce the same lexeme. The previous migration only
     * replaced ASCII apostrophe; content often uses curly/smart quote U+2019.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() !== 'pgsql' || ! Schema::hasColumn('posts', 'search_vector')) {
            return;
        }

        // Strip ASCII ', Unicode right single quote U+2019, modifier letter apostrophe U+02BC
        $norm = "replace(replace(replace(coalesce(%s, ''), '''', ''), CHR(8217), ''), CHR(700), '')";

        DB::statement('DROP INDEX IF EXISTS posts_search_vector_idx');
        DB::statement('ALTER TABLE posts DROP COLUMN IF EXISTS search_vector');
        DB::statement("
            ALTER TABLE posts
            ADD COLUMN search_vector tsvector
            GENERATED ALWAYS AS (
                setweight(to_tsvector('simple', ".sprintf($norm, 'title')."), 'A') ||
                setweight(to_tsvector('simple', ".sprintf($norm, 'content')."), 'B')
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
                setweight(to_tsvector('simple', replace(coalesce(title, ''), '''', '')), 'A') ||
                setweight(to_tsvector('simple', replace(coalesce(content, ''), '''', '')), 'B')
            ) STORED
        ");
        DB::statement('CREATE INDEX posts_search_vector_idx ON posts USING GIN (search_vector)');
    }
};
