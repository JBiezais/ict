<?php

namespace Tests\Unit\Comment\Http\Rules;

use App\Comment\Database\Models\Comment;
use App\Comment\Http\Rules\ValidCommentDepthRule;
use App\Post\Database\Models\Post;
use App\Post\Http\Controllers\PostPublicController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class ValidCommentParentRuleTest extends TestCase
{
    use RefreshDatabase;

    public function test_passes_when_value_is_null(): void
    {
        $post = Post::factory()->create();
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => null],
            ['parent_id' => [$rule]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_passes_when_post_is_null(): void
    {
        $comment = Comment::factory()->create();
        $rule = new ValidCommentDepthRule(null);

        $validator = Validator::make(
            ['parent_id' => (string) $comment->id],
            ['parent_id' => [$rule]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_passes_when_post_is_not_post_model(): void
    {
        $comment = Comment::factory()->create();
        $rule = new ValidCommentDepthRule('not-a-post-model');

        $validator = Validator::make(
            ['parent_id' => (string) $comment->id],
            ['parent_id' => [$rule]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_passes_when_value_is_not_numeric(): void
    {
        $post = Post::factory()->create();
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => 'not-numeric'],
            ['parent_id' => [$rule]]
        );

        $this->assertFalse($validator->fails());
    }

    public function test_fails_when_comment_does_not_exist(): void
    {
        $post = Post::factory()->create();
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => '99999'],
            ['parent_id' => [$rule]]
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            __('The selected comment is invalid.'),
            $validator->errors()->first('parent_id')
        );
    }

    public function test_fails_when_comment_belongs_to_different_post(): void
    {
        $post = Post::factory()->create();
        $otherPost = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $otherPost->id]);
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => (string) $comment->id],
            ['parent_id' => [$rule]]
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            __('The selected comment is invalid.'),
            $validator->errors()->first('parent_id')
        );
    }

    public function test_fails_when_parent_at_max_nesting_depth(): void
    {
        $post = Post::factory()->create();
        $current = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null]);
        for ($i = 0; $i < PostPublicController::MAX_COMMENT_NESTING_DEPTH; $i++) {
            $current = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $current->id]);
        }
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => (string) $current->id],
            ['parent_id' => [$rule]]
        );

        $this->assertTrue($validator->fails());
        $this->assertEquals(
            __('Maximum nesting level reached. You cannot reply to this comment.'),
            $validator->errors()->first('parent_id')
        );
    }

    public function test_passes_when_parent_below_max_nesting_depth(): void
    {
        $post = Post::factory()->create();
        $root = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => null]);
        $child = Comment::factory()->create(['post_id' => $post->id, 'parent_id' => $root->id]);
        $rule = new ValidCommentDepthRule($post);

        $validator = Validator::make(
            ['parent_id' => (string) $child->id],
            ['parent_id' => [$rule]]
        );

        $this->assertFalse($validator->fails());
    }

}
