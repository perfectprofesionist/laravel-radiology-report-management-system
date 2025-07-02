<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    // Define the table name (optional if you use plural convention)
    protected $table = 'files';

    // The attributes that are mass assignable
    protected $fillable = [
        'request_uuid',
        'original_name',
        'file_name',
        'file_url',
        'type',
    ];

    // If you are using UUIDs, Laravel will automatically use the `id` column as the primary key.
    // But if you want to use `request_uuid` as the primary key, you can configure it as:
    // protected $primaryKey = 'request_uuid';

    // Indicating the model uses UUID for the primary key (optional if not using UUIDs for the PK)
    public $incrementing = false;  // This tells Eloquent not to increment the primary key value
    protected $keyType = 'string'; // This sets the key type to string for UUIDs (instead of default integer)

    // The attributes that should be mutated to dates
    protected $dates = ['created_at', 'updated_at'];

    // You can also define relationships if needed (e.g., if you have a relationship with the `Request` model)
    // public function request() {
    //     return $this->belongsTo(Request::class, 'request_uuid');
    // }
}

