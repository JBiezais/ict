<?php

namespace App\Post\Console;

use App\Post\Database\Models\Post;
use Database\Factories\PostFactory;
use Illuminate\Console\Command;

class SeedPostsCommand extends Command
{
    protected $signature = 'post:seed {--user= : The user ID to assign posts to} {--count=50 : Number of posts to create}';

    public function handle(): int
    {
        $userId = $this->option('user');
        $count = (int) $this->option('count');

        /** @var PostFactory $factory */
        $factory = Post::factory($count);
        $factory->create(
            $userId ? ['user_id' => (int) $userId] : []
        );

        $this->info('Created '.$count.' posts'.($userId ? " for user {$userId}" : '').'.');

        return self::SUCCESS;
    }
}
