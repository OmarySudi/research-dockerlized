<?php

namespace App;
use Illuminate\Database\Eloquent\Model;


class AreaUser extends Model 
{
   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['area_id','user_id'];

    protected $casts = [
        
    ];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];
}