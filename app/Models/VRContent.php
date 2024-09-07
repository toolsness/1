<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VRContent extends Model
{
    use HasFactory;

    protected $fillable = [
        'content_name', 'content_category', 'content_link', 'image', 'remarks'
    ];

    const CONTENT_CATEGORIES = [
        'CompanyIntroduction' => 'Company Introduction',
        'WorkplaceTour' => 'Workplace Tour',
    ];

    protected $casts = [
        'content_category' => 'string',
    ];

    public static function getContentCategories()
    {
        return self::CONTENT_CATEGORIES;
    }

    public function vacanciesAsCompanyIntroduction()
    {
        return $this->hasMany(Vacancy::class, 'vr_content_company_introduction_id');
    }

    public function vacanciesAsWorkplaceTour()
    {
        return $this->hasMany(Vacancy::class, 'vr_content_workplace_tour_id');
    }
}
