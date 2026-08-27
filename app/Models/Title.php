<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Title extends Model
{
    use SoftDeletes;

    protected $table = 'title';

    protected $fillable = [
        'title',
        'description',
        'status',
        'slug',
        'is_favorite',
    ];

    protected $casts = [
        'status' => 'string',
        'is_favorite' => 'boolean',
    ];

    public function revisions()
    {
        return $this->hasMany(
            TitleRevision::class,
            'title_id'
        );
    }

    public function comments()
    {
        return $this->hasMany(
            TitleComment::class,
            'title_id'
        );
    }
}
