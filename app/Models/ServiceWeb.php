<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $service_id
 * @property string $url
 * @property int $server_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\Server $server
 * @property-read \App\Models\Service $service
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereServerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ServiceWeb withoutTrashed()
 * @mixin \Eloquent
 */
class ServiceWeb extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'service_webs';

    protected $primaryKey = 'service_id';

    public $incrementing = false;

    protected $fillable = [
        'service_id',
        'url',
        'server_id'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'service_id' => 'integer',
        'url' => 'string',
        'server_id' => 'integer',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];


    /**
     * Validation rules
     *
     * @var array
     */
    public static $rules = [
        'service_id' => 'required|integer|unique:service_webs,service_id',
        'url' => 'required|string|max:350',
        'server_id' => 'required|integer',
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
    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

}
