<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_id',
        'application_status',
        'reason',
        'application_date',
    ];

    public function work()
    {
        return $this->belongsTo(Work::class);
    }

    public function approvals()
    {
        return $this->hasMany(Approval::class);
    }
}
