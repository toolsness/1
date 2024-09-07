<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;
    protected $fillable = [
        'student_id', 'publish_category', 'name', 'gender', 'birth_date',
        'nationality', 'last_education', 'work_history', 'qualification', 'self_presentation',
        'personal_preference', 'profile_picture_link', 'self_introduction_video_link', 'cv_link', 'created_by', 'updated_by'
    ];

    protected $casts = [
        'birth_date' => 'date:Y-m-d',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'nationality');
    }

    public function qualificationRelation()
    {
        return $this->belongsTo(Qualification::class, 'qualification');
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
