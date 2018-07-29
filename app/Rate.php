<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'rates';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['currency',
    					   'rate',
                           'base_currency',
                           'inverse_rate',
    					   'published_at',
    					  ];
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
