<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Correction_rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'correction_work_id',
        'rest_start',
        'rest_finish',
    ];

    public function correction_work()
    {
        return $this->belongsTo(Correction_work::class);
    }
}
