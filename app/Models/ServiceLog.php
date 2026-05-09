<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 *
 *
 * @property int $id
 * @property string $status
 * @property float $response_time
 * @property int $checked_at
 * @property int $service_id
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog whereCheckedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog whereResponseTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceLog whereStatus($value)
 * @mixin \Eloquent
 */
class ServiceLog extends Model
{


    use HasFactory;

    protected $table = 'service_logs';

    public $timestamps = false;

    protected $fillable = [
        'status',
        'response_time',
        'checked_at',
        'service_id'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'status' => 'string',
        'response_time' => 'float',
        'checked_at' => 'timestamp',
        'service_id' => 'integer',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'status' => 'required|string',
        'response_time' => 'required|numeric',
        'checked_at' => 'required|date',
        'service_id' => 'required|integer',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

}
