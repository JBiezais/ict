<?php

namespace Tests\Unit\Post\Database\QueryBuilders;

use App\Post\Database\QueryBuilders\PostTsQueryBuilder;
use Tests\TestCase;

class PostTsQueryBuilderTest extends TestCase
{
    public function test_build_returns_tsquery_for_single_term(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('laravel');

        $this->assertEquals('laravel:*', $result);
    }

    public function test_build_returns_tsquery_for_multiple_terms_with_and(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('laravel framework');

        $this->assertEquals('laravel:* & framework:*', $result);
    }

    public function test_build_returns_null_for_empty_string(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('');

        $this->assertNull($result);
    }

    public function test_build_returns_null_for_whitespace_only(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('   ');

        $this->assertNull($result);
    }

    public function test_build_sanitizes_special_characters(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('foo & bar');

        $this->assertEquals('foo:* & bar:*', $result);
    }

    public function test_build_adds_prefix_wildcard_to_terms(): void
    {
        $builder = new PostTsQueryBuilder;

        $result = $builder->build('test');

        $this->assertStringEndsWith(':*', $result);
        $this->assertEquals('test:*', $result);
    }
}
