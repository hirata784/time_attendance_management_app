<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Work extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'attendance_time',
        'leaving_time',
    ];

    public function rests()
    {
        return $this->hasMany(Rest::class);
    }

    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}