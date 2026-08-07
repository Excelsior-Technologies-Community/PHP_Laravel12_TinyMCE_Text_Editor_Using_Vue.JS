<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleComment extends Model
{
    protected $table = 'title_comments';
    protected $fillable = ['title_id', 'body', 'user_name'];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
