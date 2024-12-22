<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EditContent extends Model
{
    use HasFactory;

      protected $table = 'edit_contents';
    protected $fillable = [
        'file_id',
        'copy_id',
        'content',
        
    ];
}
