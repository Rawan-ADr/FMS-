<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $table = 'user_groups';
    protected $fillable = [
        'user_id',
        'group_id',
        'joined_date',
    ];

      public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

     public function files()
    {
        return $this->hasMany(File::class, 'user_group_id');
    }
}
