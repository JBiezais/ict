<?php

namespace Tests\Feature\Post\Http\Controllers;

use App\Category\Database\Models\Category;
use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_posts_index_redirects_guest_to_login(): void
    {
        $response = $this->get(route('my-posts.posts.index'));

        $response->assertRedirect('/login');
    }

    public function test_posts_index_renders_for_authenticated_user(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('my-posts.posts.index'));

        $response->assertOk();
        $response->assertViewIs('posts.pages.manage.index');
    }

    public function test_posts_index_shows_only_user_posts(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();
        Post::factory()->create(['user_id' => $userA->id, 'title' => 'User A Post']);
        Post::factory()->create(['user_id' => $userB->id, 'title' => 'User B Post']);

        $response = $this->actingAs($userA)->get(route('my-posts.posts.index'));

        $response->assertSee('User A Post');
        $response->assertDontSee('User B Post');
    }

    public function test_posts_index_displays_category_labels_for_categorized_posts(): void
    {
        $user = User::factory()->create();
        $tech = Category::factory()->create(['name' => 'Tech']);
        $post = Post::factory()->create(['user_id' => $user->id, 'title' => 'Categorized Post']);
        $post->categories()->attach($tech->id);

        $response = $this->actingAs($user)->get(route('my-posts.posts.index'));

        $response->assertOk();
        $response->assertSee('Tech');
    }

    public function test_posts_index_displays_uncategorized_for_posts_without_categories(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'No Categories Post']);

        $response = $this->actingAs($user)->get(route('my-posts.posts.index'));

        $response->assertOk();
        $response->assertSee('Uncategorized');
    }

    public function test_posts_index_returns_partial_fragment_when_ajax_request_with_fragment(): void
    {
        $user = User::factory()->create();
        Post::factory()->create(['user_id' => $user->id, 'title' => 'Fragment Post']);

        $response = $this->actingAs($user)
            ->withHeader('X-Requested-With', 'XMLHttpRequest')
            ->get(route('my-posts.posts.index', ['_fragment' => '1']));

        $response->assertOk();
        $response->assertViewIs('posts.components.manage-list');
    }

    public function test_posts_create_renders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('my-posts.posts.create'));

        $response->assertOk();
        $response->assertViewIs('posts.pages.create');
        $response->assertViewHas('categories');
    }

    public function test_posts_store_creates_post_and_redirects(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('my-posts.posts.store'), [
            'title' => 'New Post Title',
            'content' => 'New post content here.',
        ]);

        $response->assertRedirect(route('my-posts.posts.index'));
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('posts', [
            'user_id' => $user->id,
            'title' => 'New Post Title',
            'content' => 'New post content here.',
        ]);
    }

    public function test_posts_store_with_categories_creates_post_and_attaches_categories(): void
    {
        $user = User::factory()->create();
        $tech = Category::factory()->create(['name' => 'Tech']);
        $laravel = Category::factory()->create(['name' => 'Laravel']);
        $php = Category::factory()->create(['name' => 'PHP']);

        $response = $this->actingAs($user)->post(route('my-posts.posts.store'), [
            'title' => 'Post With Categories',
            'content' => 'Content',
            'category_ids' => [$tech->id, $laravel->id, $php->id],
        ]);

        $response->assertRedirect(route('my-posts.posts.index'));

        $post = Post::where('title', 'Post With Categories')->first();
        $this->assertNotNull($post);
        $post->load('categories');
        $this->assertCount(3, $post->categories);
        $this->assertTrue($post->categories->pluck('name')->contains('Tech'));
        $this->assertTrue($post->categories->pluck('name')->contains('Laravel'));
        $this->assertTrue($post->categories->pluck('name')->contains('PHP'));
    }

    public function test_posts_store_fails_validation(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('my-posts.posts.store'), [
            'title' => '',
            'content' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'content']);
    }

    public function test_posts_edit_renders_for_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('my-posts.posts.edit', $post));

        $response->assertOk();
        $response->assertViewIs('posts.pages.edit');
        $response->assertSee($post->title);
        $response->assertViewHas('categories');
    }

    public function test_posts_edit_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->get(route('my-posts.posts.edit', $post));

        $response->assertForbidden();
    }

    public function test_posts_update_succeeds_for_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create([
            'user_id' => $user->id,
            'title' => 'Old Title',
            'content' => 'Old content',
        ]);

        $response = $this->actingAs($user)->put(route('my-posts.posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content.',
        ]);

        $response->assertRedirect(route('my-posts.posts.index'));
        $response->assertSessionHas('status');
        $post->refresh();
        $this->assertEquals('Updated Title', $post->title);
        $this->assertEquals('Updated content.', $post->content);
    }

    public function test_posts_update_with_categories_syncs_categories(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $tech = Category::factory()->create(['name' => 'Tech']);
        $laravel = Category::factory()->create(['name' => 'Laravel']);
        $php = Category::factory()->create(['name' => 'PHP']);
        $post->categories()->attach($tech->id);

        $response = $this->actingAs($user)->put(route('my-posts.posts.update', $post), [
            'title' => $post->title,
            'content' => $post->content,
            'category_ids' => [$tech->id, $laravel->id, $php->id],
        ]);

        $response->assertRedirect(route('my-posts.posts.index'));

        $post->refresh();
        $post->load('categories');
        $this->assertCount(3, $post->categories);
        $this->assertTrue($post->categories->pluck('name')->contains('Tech'));
        $this->assertTrue($post->categories->pluck('name')->contains('Laravel'));
        $this->assertTrue($post->categories->pluck('name')->contains('PHP'));
    }

    public function test_posts_update_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->put(route('my-posts.posts.update', $post), [
            'title' => 'Hacked Title',
            'content' => 'Hacked content',
        ]);

        $response->assertForbidden();
    }

    public function test_posts_destroy_succeeds_for_owner(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete(route('my-posts.posts.destroy', $post));

        $response->assertRedirect(route('my-posts.posts.index'));
        $response->assertSessionHas('status');
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_posts_destroy_denied_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($otherUser)->delete(route('my-posts.posts.destroy', $post));

        $response->assertForbidden();
        $this->assertDatabaseHas('posts', ['id' => $post->id]);
    }
}
