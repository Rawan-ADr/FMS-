<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    use HasFactory;

     protected $table = 'file_logs';
    protected $fillable = [
        'action',
        'file_id',
        'user_id',
        'edit_id',
        'date'
    ];


     public function file()
    {
        return $this->belongsTo(File::class);
    }
}
