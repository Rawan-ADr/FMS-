<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\FileLog;
use App\Models\Group;

class File extends Model
{
    use HasFactory;
     protected $table = 'files';
       protected $fillable = [
        'name',
        'type',
        'path',
       'size',
       'state',
        'user_groups'
      
    ];

    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

     public function fileLogs()
    {
        return $this->hasMany(FileLog::class);
    }

      public function userLogs()
    {
        return $this->hasMany(FileLog::class);
    }
      public function fileCopies()
    {
        return $this->hasMany(FileCopy::class);
    }

}
