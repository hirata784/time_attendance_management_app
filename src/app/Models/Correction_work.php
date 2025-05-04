<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correction_work extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_id',
        'application_status',
        'attendance_time',
        'leaving_time',
        'remarks',
        'application_date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
