<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Group extends Model
{
    use HasFactory;
    protected $guarded = [];

    protected $table = 'groups';
    protected $fillable = [
        'name',
        'creation_date',
        'user_id'
    ];

    public function user(){
        return $this->belongsToMany(User::class,'user_groups')
        ->withPivot('joined_date')
        ->withTimestamps();  

}
}
