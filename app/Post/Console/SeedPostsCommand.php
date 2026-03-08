<?php

namespace App\Post\Console;

use App\Category\Database\Models\Category;
use App\Comment\Database\Models\Comment;
use App\Post\Database\Models\Post;
use App\Post\Http\Controllers\PostPublicController;
use App\User\Database\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SeedPostsCommand extends Command
{
    protected $signature = 'post:seed {--user= : The user ID to assign posts to} {--count=25 : Number of posts to create}';

    /**
     * @var array<string>
     */
    private const CATEGORY_NAMES = [
        'Technology',
        'PHP',
        'Laravel',
        'Web Development',
        'Programming',
        'Design',
        'Tutorials',
    ];

    /**
     * @var array<string>
     */
    private const TITLE_TEMPLATES = [
        'Getting Started with Laravel',
        'Understanding PHP Dependency Injection',
        'How to Build REST APIs in Laravel',
        '5 Tips for Cleaner Code',
        'Introduction to Web Security Best Practices',
        'Debugging Techniques Every Developer Should Know',
        'Eloquent Relationships Made Simple',
        'Testing Your Laravel Applications',
        'A Guide to Laravel Queues and Jobs',
        'PHP 8 Features You Should Use',
        'Design Patterns in Modern PHP',
        'Optimizing Database Queries in Laravel',
        'Building a Blog with Laravel',
        'Authentication and Authorization in Laravel',
        'Working with JSON in PHP',
        'Introduction to Composer and Autoloading',
        'RESTful API Design Best Practices',
        'Laravel Middleware Explained',
        'Error Handling and Logging Strategies',
        'Deploying Laravel Applications',
        'Understanding the Request Lifecycle',
        'Code Review Best Practices',
        'Refactoring Legacy PHP Code',
        'Introduction to Server-Side Rendering',
        'Caching Strategies for Web Applications',
        'Writing Maintainable PHP Code',
        'Laravel Validation Rules in Depth',
        'Working with Files and Storage',
        'Introduction to Event-Driven Architecture',
        'Database Migrations and Schema Design',
        'API Versioning Strategies',
        'Performance Optimization Techniques',
        'Introduction to Docker for PHP Developers',
        'SOLID Principles in Practice',
        'Working with External APIs',
        'Laravel Form Requests Explained',
        'Introduction to TDD in PHP',
        'Managing Configuration and Environments',
        'Creating Reusable PHP Components',
    ];

    public function handle(): int
    {
        $userId = $this->option('user');
        $count = (int) $this->option('count');

        if ($count < 0) {
            $this->error('Count must be non-negative.');

            return self::FAILURE;
        }

        $postAuthorIds = $this->ensurePostAuthors($userId);
        $commenterIds = $this->ensureCommenters();
        $categoryIds = $this->ensureCategories();

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        for ($i = 0; $i < $count; $i++) {
            $post = $this->createPostWithContent(
                $postAuthorIds->random()
            );
            $this->attachCategories($post, $categoryIds);

            if (fake()->boolean(65)) {
                $this->createCommentsForPost($post, $commenterIds);
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Created '.$count.' posts'.($userId ? " for user {$userId}" : '').'.');

        return self::SUCCESS;
    }

    /**
     * @return Collection<int, int>
     */
    private function ensurePostAuthors(?string $userId): Collection
    {
        if ($userId !== null) {
            $id = (int) $userId;
            $user = User::find($id);
            if ($user === null) {
                throw new \RuntimeException("User with ID {$userId} not found.");
            }

            return collect([$id]);
        }

        $this->ensureMinimumUsers(5);

        /** @var \Illuminate\Support\Collection<int, int> $userIds */
        $userIds = User::inRandomOrder()->limit(5)->pluck('id')->map(
            /** @phpstan-ignore argument.type */
            fn (mixed $id): int => is_int($id) ? $id : (int) strval($id)
        );
        $numAuthors = fake()->numberBetween(3, min(5, $userIds->count()));

        return $userIds->take($numAuthors)->values();
    }

    /**
     * @return Collection<int, int>
     */
    private function ensureCommenters(): Collection
    {
        $this->ensureMinimumUsers(5);

        return User::inRandomOrder()->limit(5)->pluck('id')->map(
            /** @phpstan-ignore argument.type */
            fn (mixed $id): int => is_int($id) ? $id : (int) strval($id)
        )->values();
    }

    private function ensureMinimumUsers(int $min): void
    {
        $current = User::count();
        if ($current < $min) {
            /** @var \Illuminate\Database\Eloquent\Factories\Factory<\App\User\Database\Models\User> $factory */
            $factory = User::factory($min - $current);
            $factory->create();
        }
    }

    /**
     * @return Collection<int, int>
     */
    private function ensureCategories(): Collection
    {
        /** @var \Illuminate\Support\Collection<int, int> $ids */
        $ids = collect();
        foreach (self::CATEGORY_NAMES as $name) {
            $category = Category::firstOrCreate(['name' => $name]);
            $ids->push((int) $category->id);
        }

        return $ids;
    }

    private function createPostWithContent(int $userId): Post
    {
        return Post::create([
            'user_id' => $userId,
            'title' => $this->generateTitle(),
            'content' => $this->generateContent(),
        ]);
    }

    private function generateTitle(): string
    {
        $title = fake()->randomElement(self::TITLE_TEMPLATES);

        return is_string($title) ? $title : '';
    }

    private function generateContent(): string
    {
        $paragraphs = fake()->numberBetween(2, 4);
        $parts = [];
        for ($i = 0; $i < $paragraphs; $i++) {
            $parts[] = fake()->realText(300, 3);
        }

        return implode("\n\n", $parts);
    }

    /**
     * @param  Collection<int, int>  $categoryIds
     */
    private function attachCategories(Post $post, Collection $categoryIds): void
    {
        $numCategories = fake()->numberBetween(1, min(3, $categoryIds->count()));
        $selected = $categoryIds->random($numCategories);

        $post->categories()->attach($selected->all());
    }

    /**
     * @param  Collection<int, int>  $commenterIds
     */
    private function createCommentsForPost(Post $post, Collection $commenterIds): void
    {
        $numComments = fake()->numberBetween(1, 15);
        $maxDepth = PostPublicController::MAX_COMMENT_NESTING_DEPTH - 1;

        /** @var array<int, int> */
        $commentDepths = [];

        for ($i = 0; $i < $numComments; $i++) {
            $repliable = array_filter(
                $commentDepths,
                fn (int $depth) => $depth < $maxDepth
            );
            $isRoot = $repliable === [] || fake()->boolean(70);

            if ($isRoot) {
                $parentId = null;
                $depth = 0;
            } else {
                $parentIdKey = fake()->randomElement(array_keys($repliable));
                /** @phpstan-ignore argument.type */
                $parentId = is_int($parentIdKey) ? $parentIdKey : (int) strval($parentIdKey);
                $depth = $commentDepths[$parentId] + 1;
            }

            $commenterPool = $commenterIds->all();
            if ($parentId !== null && fake()->boolean(30)) {
                $commenterPool = array_values(array_unique([...$commenterPool, $post->user_id]));
            }

            $comment = Comment::create([
                'post_id' => $post->id,
                'user_id' => fake()->randomElement($commenterPool),
                'parent_id' => $parentId,
                'content' => fake()->realText(150, 2),
            ]);

            $commentDepths[$comment->id] = $depth;
        }
    }
}
