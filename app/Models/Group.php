<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\File;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'groups';
    protected $fillable = [
        'name',
        'creation_date',
        'user_id',
        'description',
        'NumOfUser'
    ];

    protected $hidden = [
       
        'pivot'
    ];

    public function user(){
        return $this->belongsToMany(User::class,'user_groups')
        ->withPivot('joined_date')
        ->withTimestamps();  

}

 


      public function userGroups()
    {
        return $this->hasMany(UserGroup::class, 'group_id');
    }


    public function files()
    {
        return $this->hasManyThrough(File::class, UserGroup::class);
    }
}
