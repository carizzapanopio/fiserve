<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'currencies';

    /**
     * Indicates if the primary key of the table
     *
     * @var string
     */
    protected $primaryKey    = 'code';
    /**
     * Indicates if the primary key should be incrementing
     *
     * @var bool
     */
    public    $incrementing  = false;
    /**
     * Indicates the data type of the primary key
     *
     * @var string
     */
    protected $keyType       = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
                            'code',
    				        'name',
    					  ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;
}
