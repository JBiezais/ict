<?php

namespace App\Comment\Database\Models;

use App\Post\Database\Models\Post;
use App\User\Database\Models\User;
use Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property string $uuid
 * @property int $post_id
 * @property int $user_id
 * @property int|null $parent_id
 * @property string $content
 * @property int $children_count
 */
class Comment extends Model
{
    /** @use HasFactory<CommentFactory> */
    use HasFactory;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'id',
    ];

    /**
     * @return Factory<Comment>
     */
    protected static function newFactory(): Factory
    {
        return CommentFactory::new();
    }

    protected static function booted(): void
    {
        static::creating(function (Comment $model): void {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * @var list<string>
     */
    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'content',
    ];

    /**
     * @return BelongsTo<Post, $this>
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Comment, $this>
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    /**
     * @return HasMany<Comment, $this>
     */
    public function children(): HasMany
    {
        return $this->hasMany(Comment::class, 'parent_id')->latest();
    }
}
