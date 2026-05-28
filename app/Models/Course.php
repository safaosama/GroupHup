<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Section;
use App\Models\Group;

class Course extends Model
{
    protected $fillable = [
        'name',
        'min_students',
        'max_students',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function groups()
    {
        return $this->hasManyThrough(Group::class, Section::class);
    }
}
