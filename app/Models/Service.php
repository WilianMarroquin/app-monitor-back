<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property string $type
 * @property int $is_active
 * @property string|null $testMethod
 * @property string|null $httpMethod
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereHttpMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereTestMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Service withoutTrashed()
 * @property-read \App\Models\ServiceDatabase|null $detalleDataBase
 * @property-read \App\Models\ServiceWeb|null $detalleWeb
 * @property-read \App\Models\Server|null $server
 * @property int|null $port
 * @property int|null $tiempo_espera
 * @property string|null $entorno
 * @property int|null $server_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Area> $areas
 * @property-read int|null $areas_count
 * @property-read array $contactos
 * @method static Builder<static>|Service soloActivos()
 * @method static Builder<static>|Service whereEntorno($value)
 * @method static Builder<static>|Service wherePort($value)
 * @method static Builder<static>|Service whereServerId($value)
 * @method static Builder<static>|Service whereTiempoEspera($value)
 * @mixin \Eloquent
 */
class Service extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'services';


    protected $fillable = [
        'name',
        'description',
        'type',
        'is_active',
        'testMethod',
        'httpMethod',
        'port',
        'tiempo_espera',
        'entorno',
        'server_id',
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'description' => 'string',
        'type' => 'string',
        'is_active' => 'boolean',
        'testMethod' => 'string',
        'httpMethod' => 'string',
        'port' => 'integer',
        'tiempo_espera' => 'integer',
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
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'type' => 'required|string',
        'is_active' => 'required',
        'testMethod' => 'nullable|string|max:45',
        'httpMethod' => 'nullable|string|max:45',
        'service_web' => 'nullable',
        'service_database' => 'nullable',
        'port' => 'nullable|string|max:10',
        'tiempo_espera' => 'nullable|integer',
        'entorno' => 'nullable|string|in:Desarrollo,Produccion',
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

    public function detalleWeb(): HasOne
    {
        return $this->hasOne(ServiceWeb::class, 'service_id');
    }

    public function detalleDataBase(): HasOne
    {
        return $this->hasOne(ServiceDataBase::class, 'service_id');
    }

    public function server(): BelongsTo
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class,
            'area_service',
            'service_id',
            'area_id'
        );
    }

    public function scopeSoloActivos(Builder $query)
    {
        return $query->where('is_active', true);
    }

    public function getContactosAttribute(): array
    {
        $contactos = [];
        /**
         * @var Service $this
         * @var Area $area
         */
        foreach ($this->areas as $area) {
            foreach ($area->contactosAsignados as $contacto) {
                $contactos[] = [
                    'name' => $contacto->nombre_completo,
                    'telefono' => $contacto->telefono,
                ];
            }
        }

        return $contactos;
    }
}
