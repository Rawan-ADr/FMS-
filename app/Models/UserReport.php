<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReport extends Model
{
    use HasFactory;
    protected $table = 'user_reports';
       protected $fillable = [
       'description',
       'user_id',
       'group_id'
      
      
    ];
}
