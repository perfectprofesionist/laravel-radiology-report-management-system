<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Chat extends Model
{
    use HasFactory;

    protected $fillable = [
        'request_listing_id',
        'user_id',
        'message',
        'type',
    ];

    /**
     * Get the user who sent the message.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the related request listing (case).
     */
    public function requestListing()
    {
        return $this->belongsTo(RequestListing::class, 'request_listing_id');
    }
}
