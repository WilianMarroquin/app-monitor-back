<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * 
 *
 * @property int $id
 * @property string $nombres
 * @property string $apellidos
 * @property string $telefono
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereApellidos($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereNombres($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereTelefono($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact withoutTrashed()
 * @property-read string $nombre_completo
 * @method static \Illuminate\Database\Eloquent\Builder<static>|NotificationContact buscarPorNombreCompleto($nombreCompleto)
 * @mixin \Eloquent
 */
class NotificationContact extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'notification_contacts';


    protected $fillable = [
        'nombres',
        'apellidos',
        'telefono'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'nombres' => 'string',
        'apellidos' => 'string',
        'telefono' => 'string',
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
        'nombres' => 'required|string|max:150',
        'apellidos' => 'required|string|max:150',
        'telefono' => 'required|string|max:8',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    protected $appends = [
        'nombre_completo'
    ];


    /**
     * Accessor for relationships
     *
     * @var array
     */

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    public function scopeBuscarPorNombreCompleto($builder, $nombreCompleto)
    {
        return $builder->whereRaw("CONCAT(nombres, ' ', apellidos) LIKE ?", ["%$nombreCompleto%"]);
    }

    public function ScopeSinPersonasAsignadasIds($builder, $ids)
    {
        $idsArray = explode('-', $ids);
        return $builder->whereNotIn('id', $idsArray);
    }

}
