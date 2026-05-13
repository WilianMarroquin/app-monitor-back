<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 *
 *
 * @property int $id
 * @property string $description
 * @property string $status
 * @property int $opened_at
 * @property int|null $resolved_at
 * @property int $service_id
 * @property int|null $ping_id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property-read \App\Models\Service $service
 * @property-read \App\Models\ServiceLog|null $serviceLog
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereOpenedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident wherePingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereResolvedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereServiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Incident withoutTrashed()
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\IncidentComment> $comentarios
 * @property-read int|null $comentarios_count
 * @property-read \App\Models\ServiceLog|null $log
 * @mixin \Eloquent
 */
class Incident extends Model
{

    use SoftDeletes;
    use HasFactory;

    protected $table = 'incidents';


    protected $fillable = [
        'description',
        'status',
        'opened_at',
        'resolved_at',
        'service_id',
        'ping_id'
    ];


    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'description' => 'string',
        'status' => 'string',
        'opened_at' => 'timestamp',
        'resolved_at' => 'timestamp',
        'service_id' => 'integer',
        'ping_id' => 'integer',
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
        'description' => 'required|string',
        'status' => 'required|string',
        'opened_at' => 'required|date',
        'resolved_at' => 'nullable|date',
        'service_id' => 'required|integer',
        'ping_id' => 'nullable|integer',
    ];


    /**
     * Custom messages for validation
     *
     * @var array
     */
    public static $messages = [

    ];

    protected $appends = [
        'description_corta'
    ];

    /**
     * Accessor for relationships
     *
     * @var array
     */
    public function log(): BelongsTo
    {
        return $this->belongsTo(ServiceLog::class, 'ping_id', 'id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }

    public function comentarios(): HasMany
    {
        return $this->hasMany(IncidentComment::class, 'incident_id', 'id')->orderBy('id', 'desc');

    }

    public function contactosNotificados(): BelongsToMany
    {
        return $this->belongsToMany(
            NotificationContact::class,
            'incident_has_notificacion',
            'incident_id',
            'notification_contact_id')
            ->withPivot('status', 'number');

    }

    public function getDescriptionCortaAttribute(): string|null
    {
        return strlen($this->description) > 80 ? substr($this->description, 0, 80) . '...' : $this->description;

    }

    /**
     * Scope para filtrar por rango de fechas exacto (YYYY-MM-DD-YYYY-MM-DD)
     */
    public function scopeRangoFechas($query, $rangoFechas)
    {
        if (empty($rangoFechas)) {
            return $query;
        }

        $startDate = null;
        $endDate = null;

        // 🧮 STRING PARSING: Extraemos las fechas basándonos en la longitud exacta
        if (strlen($rangoFechas) === 21) {
            // Rango completo: "2026-05-11-2026-05-15"
            $startDate = substr($rangoFechas, 0, 10);
            $endDate = substr($rangoFechas, 11, 10);
        } elseif (strlen($rangoFechas) === 11) {
            // Rango parcial
            if (str_starts_with($rangoFechas, '-')) {
                // Solo fecha fin: "-2026-05-15"
                $endDate = substr($rangoFechas, 1, 10);
            } else {
                // Solo fecha inicio: "2026-05-11-"
                $startDate = substr($rangoFechas, 0, 10);
            }
        }

        // 🛡️ BOUNDARY INCLUSION: Aplicamos los filtros a la base de datos
        if ($startDate) {
            // ->startOfDay() asegura que busque desde las 00:00:00
            $query->where('opened_at', '>=', \Carbon\Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            // ->endOfDay() asegura que busque hasta las 23:59:59
            $query->where('opened_at', '<=', \Carbon\Carbon::parse($endDate)->endOfDay());
        }

        return $query;
    }

}
