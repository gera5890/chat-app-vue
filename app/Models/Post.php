<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'text',
        'image',
        'visibility',
        'user_id'
    ];

    public function comments(): HasMany{
        return $this->hasMany(Comment::class, 'post_id');
    }

    public function likes() : HasMany{
        return $this->hasMany(Like::class, 'post_id');
    }

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}
