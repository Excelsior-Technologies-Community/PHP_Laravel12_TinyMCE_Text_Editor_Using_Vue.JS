<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Title extends Model
{
    use SoftDeletes;

    protected $table = 'title';
    protected $fillable = ['title', 'description', 'status', 'slug'];

    protected $casts = [
        'status' => 'string',
    ];

    public function revisions()
    {
        return $this->hasMany(TitleRevision::class);
    }

    public function comments()
    {
        return $this->hasMany(TitleComment::class);
    }
}
