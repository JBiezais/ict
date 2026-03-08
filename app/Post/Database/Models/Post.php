<?php

namespace App\Post\Database\Models;

use App\Category\Database\Models\Category;
use App\Comment\Database\Models\Comment;
use App\Post\Database\QueryBuilders\PostQueryBuilder;
use App\Post\Database\QueryBuilders\PostTsQueryBuilder;
use App\User\Database\Models\User;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property int $user_id
 * @property string $title
 * @property string $content
 * @property Carbon|null $created_at
 * @property int $comments_count
 */
class Post extends Model
{
    /** @use HasFactory<PostFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * @return Factory<Post>
     */
    protected static function newFactory(): Factory
    {
        return PostFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Post $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    public function newEloquentBuilder($query): PostQueryBuilder
    {
        return new PostQueryBuilder($query, app(PostTsQueryBuilder::class));
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'title',
        'content',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * @return BelongsToMany<Category, $this>
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'post_category');
    }
}
