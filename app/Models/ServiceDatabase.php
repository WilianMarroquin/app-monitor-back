<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute; // <-- 1. Importamos Attribute
use Illuminate\Support\Facades\Crypt;            // <-- 2. Importamos Crypt

class ServiceDatabase extends Model
{
    use SoftDeletes;
    use HasFactory;

    protected $table = 'service_databases';

    protected $fillable = [
        'service_id',
        'db_type',
        'host_ip',
        'port',
        'username',
        'password'
    ];

    protected $primaryKey = 'service_id';

    public $incrementing = false;

//    protected $hidden = [
//        'password',
//    ];

    protected $casts = [
        'service_id' => 'integer',
        'db_type' => 'string',
        'host_ip' => 'string',
        'port' => 'string',
        'username' => 'string',
        'password' => 'string',
        'created_at' => 'timestamp',
        'updated_at' => 'timestamp',
        'deleted_at' => 'timestamp',
    ];

    public static $rules = [
        'service_id' => 'required|integer|unique:service_databases,service_id',
        'db_type' => 'required|string|max:65',
        'host_ip' => 'required|string|max:350',
        'port' => 'required|string|max:10',
        'username' => 'required|string|max:150',
        'password' => 'required|string|max:255',
    ];

    public static $messages = [

    ];

    // === 4. EL ACCESOR MAGICO ===
    // Cuando llames a $serviceDatabase->password, Laravel ejecutará esto automáticamente
    protected function password(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                try {
                    // Si hay un valor, lo desencripta. Si no, devuelve null.
                    return $value ? Crypt::decryptString($value) : null;
                } catch (\Exception $e) {
                    // Previene que la app explote si intentas leer un dato viejo
                    // que se guardó sin encriptar antes de implementar esta seguridad.
                    return $value;
                }
            }
        );
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id', 'id');
    }
}
