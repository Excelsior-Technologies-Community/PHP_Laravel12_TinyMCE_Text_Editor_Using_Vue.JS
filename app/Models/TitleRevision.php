<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TitleRevision extends Model
{
    protected $table = 'title_revisions';
    protected $fillable = ['title_id', 'title', 'description', 'changed_by'];
}
