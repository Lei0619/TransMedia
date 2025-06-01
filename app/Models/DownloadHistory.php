<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DownloadHistory extends Model
{
    use HasFactory;

    protected $table = 'download_history';

    protected $fillable = [
        'user_id',
        'conversion_id',
        'downloaded_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class);
    }
}
