<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kodeine\Metable\Metable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, softDeletes, Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'uuid',
        'username',
        'email',
        'password',
        'is_active',
        'avatar',
        'stripe_customer_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Define the meta table name (optional)
     */
    protected $metaTable = 'users_meta';

    /**
     * Define default meta values
     */
    public $defaultMetaValues = [
        'dentist_name' => '',
        'gdc_number' => '',
        'practice_name' => '',
        'practice_address' => '',
        'routine_phone' => '',
        'urgent_phone' => '',
        
        'mobile_number' => '',
        'insurance_expired_date' => '',
        'next_appraisal_date' => '',
        'home_address' => '',
        'home_post_code' => '',
        'hospital_name' => '',
        'hospital_address' => '',
        'hospital_post_code' => '',
    ];
}