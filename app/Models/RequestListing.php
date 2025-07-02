<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RequestListing extends Model
{
    use HasFactory;

    // Define the table associated with the model
    protected $table = 'request_listing';

    // The attributes that are mass assignable
    protected $fillable = [
        'uuid',
        'user_id',
        'exam_id',
        'patient_name',
        'patient_phone',
        'patient_postcode',
        'patient_address',
        'patient_email',
        'clinical_history',
        'appointment',
        'patient_dob',
        'clinical_details',
        'scan_file',
        'scan_date',
        'modality',
        'status',
        'notes',
        'notes_status',
        'pending_notes',
        'notes_approved_at',
        'notes_approved_by',
        'notes_updated_by',
        'notes_updated_at',
        'rejection_comment',
        'rejected_at',
        'rejected_by',
        'payment_amount',
        'payment_status',
        'doctor_notes',
        "question",
        'approval_comment',
        'pending_status',
        'pending_status_value',
        'status_updated_by',
        'status_updated_at',
        'status_rejected_by',
        'status_rejected_at',
        'status_approved_comment',
        'status_rejection_comment',
        'status_approved_by',
        'status_approved_at',
    ];

    // The attributes that should be cast to native types
    protected $casts = [
        'scan_date' => 'date',
        'payment_amount' => 'decimal:2',
        'notes_approved_at' => 'datetime',
        'notes_updated_at' => 'datetime',
        'rejected_at' => 'datetime',
        'approval_comment' => 'string',
        'status_updated_at' => 'datetime',
    ];

    // The attributes that should be hidden for arrays
    protected $hidden = [
        'uuid',
    ];

    // // Automatically create UUID when creating new record (if not provided)
    // protected static function booted()
    // {
    //     static::creating(function ($model) {
    //         if (!$model->uuid) {
    //             $model->uuid = (string) Str::uuid();
    //         }
    //     });
    // }

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'notes_updated_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'notes_approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function notesHistory()
    {
        return $this->hasMany(NotesHistory::class, 'request_uuid', 'uuid');
    }

    public function statusHistory()
    {
        return $this->hasMany(StatusHistory::class, 'request_uuid', 'uuid');
    }

    public function statusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }

    public function statusApprovedBy()
    {
        return $this->belongsTo(User::class, 'status_approved_by');
    }

}
