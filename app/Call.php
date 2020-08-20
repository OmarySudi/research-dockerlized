<?php

namespace App;
use Illuminate\Database\Eloquent\Model;


class Call extends Model 
{
   

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'funder_id',
        'address',
        'currency',
        'budget',
        'deadline',
        'status',
        'other_areas',
        'description'
    ];

    protected $casts = [
        //'areas_of_research_names' => 'array'
    ];
    
    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
       
    ];
}