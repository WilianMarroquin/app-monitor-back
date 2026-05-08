<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string|null $internal_ip
 * @property string|null $external_ip
 * @property string|null $entorno
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereEntorno($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereExternalIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereInternalIp($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Server withoutTrashed()
 * @mixin \Eloquent
 */
class Server extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'servers';


    protected $fillable =
        [
    'name',
    'description',
    'internal_ip',
    'external_ip',
    'entorno'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'internal_ip' => 'string',
        'external_ip' => 'string',
        'entorno' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];



    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules =
    [
    'name' => 'required|string|max:150',
    'description' => 'nullable|string',
    'internal_ip' => 'nullable|string|max:15',
    'external_ip' => 'nullable|string|max:15',
    'entorno' => 'nullable|string',
];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages =[

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    

}
