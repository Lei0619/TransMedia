<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_filename',
        'converted_filename',
        'original_format',
        'target_format',
        'source_type',
        'source_url',
        'status',
        'error_message',
        'file_size',
        'duration',
        'metadata',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function downloadHistory(): HasMany
    {
        return $this->hasMany(DownloadHistory::class);
    }

    public function getFileSizeHumanAttribute(): string
    {
        if (!$this->file_size) return 'Unknown';

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getDurationHumanAttribute(): string
    {
        if (!$this->duration) return 'Unknown';

        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}