<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ['name', 'section_id', 'created_by',  'is_random'];

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->hasOneThrough(
            Course::class,
            Section::class,
            'id',
            'id',
            'section_id',
            'course_id'
        );
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // عدد الأعضاء الحالي
    public function memberCount()
    {
        return $this->members()->count();
    }
}
