<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

     protected $fillable = [
        'user_id',
        'display_name',
        'job_type',
        'prefecture',
        'skills',
        'bio',
        'purpose',
        'work_style',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
