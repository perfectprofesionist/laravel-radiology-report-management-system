<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotesHistory extends Model
{
    protected $table = 'notes_history';

    protected $fillable = [
        'request_uuid',
        'notes_content',
        'status',
        'comment',
        'updated_by'
    ];

    public function request(): BelongsTo
    {
        return $this->belongsTo(RequestListing::class, 'request_uuid', 'uuid');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
