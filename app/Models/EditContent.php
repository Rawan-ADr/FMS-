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

     public function file()
    {
        return $this->belongsTo(File::class);
    }
    
     public function fileCopies()
    {
        return $this->belongsTo(FileCopy::class);
    }
}
