<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLog extends Model
{
    use HasFactory;

     protected $table = 'user_logs';
    protected $fillable = [
        'action',
        'user_id',
        'file_id',
        'date'
    ];

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
