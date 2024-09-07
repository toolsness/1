<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Qualification extends Model
{
    use HasFactory;

    protected $fillable = [
        'qualification_category_id', 'qualification_name'
    ];

    public function qualificationCategory()
    {
        return $this->belongsTo(QualificationCategory::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'qualification');
    }
}
