<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BusinessOperator extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name_kanji', 'name_katakana', 'contact_phone_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
