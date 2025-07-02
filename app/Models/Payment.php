<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    /**
     * The table associated with the model.
     */
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'request_listing_id',
        'stripe_charge_id',
        'amount',
        'currency',
        'status',
        'payment_method',
        'receipt_url',
        'paid_at',
        'failure_message',
    ];

    /**
     * The attributes that should be cast to native types.
     */
    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'decimal:2',
    ];

    /**
     * Relationship: Payment belongs to a request listing.
     */
    public function requestListing(): BelongsTo
    {
        return $this->belongsTo(RequestListing::class);
    }
}
