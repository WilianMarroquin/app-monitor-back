<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $description
 * @property int $incident_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\Incident $incident
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereIncidentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment withoutTrashed()
 * @property int $user_id
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IncidentComment whereUserId($value)
 * @mixin \Eloquent
 */
class IncidentComment extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'incident_comments';


    protected $fillable =
        [
    'description',
    'incident_id',
    'user_id'
];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts =
        [
        'id' => 'integer',
        'description' => 'string',
        'incident_id' => 'integer',
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
    'description' => 'required|string',
    'incident_id' => 'required|integer',
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
    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class,'incident_id','id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'user_id','id');

    }

}
