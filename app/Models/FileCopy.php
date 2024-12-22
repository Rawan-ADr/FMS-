<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileCopy extends Model
{
    use HasFactory;
      protected $table = 'file_copies';
       protected $fillable = [
        'name',
        'type',
        'path',
        'size',
        'file_id',
        'copyNum'
      
      
    ];

      public function file()
    {
        return $this->belongsTo(File::class);
    }
}
