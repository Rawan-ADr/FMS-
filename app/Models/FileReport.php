<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileReport extends Model
{
    use HasFactory;
    protected $table = 'file_reports';
       protected $fillable = [
       'description',
       'file_id',
       'group_id'
      
      
    ];
}
