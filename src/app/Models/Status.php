<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'work_status',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }
}
