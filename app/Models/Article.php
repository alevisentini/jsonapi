<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory;

    public $resourceType = 'articles';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'category_id' => 'integer',
        'user_id' => 'string',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeYear(Builder $query, int $year)
    {
        $query->whereYear('created_at', $year);
    }

    public function scopeMonth(Builder $query, int $month)
    {
        $query->whereMonth('created_at', $month);
    }

    public function scopeCategories(Builder $query, $categories)
    {
        $categorySlugs = explode(',', $categories);
        
        $query->whereHas('category', function ($q) use ($categorySlugs) {
            $q->whereIn('slug', $categorySlugs);
        });
    }

    public function scopeTitle(Builder $query, string $title)
    {
        $query->where('title', 'like', "%{$title}%");
    }

    public function scopeContent(Builder $query, string $content)
    {
        $query->where('content', 'like', "%{$content}%");
    }

}
