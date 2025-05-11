<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Classroom extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'years',
        'type',
        'size',
        'school_id',
        'cursus_id',
        'level_id',
        'gender',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the cursus that owns the classroom.
     */
    public function cursus(): BelongsTo
    {
        return $this->belongsTo(Cursus::class);
    }

    /**
     * Get the level that owns the classroom.
     */
    public function level(): BelongsTo
    {
        return $this->belongsTo(CursusLevel::class, 'level_id');
    }

    /**
     * Get all user roles for this classroom.
     */
    public function userRoles(): MorphMany
    {
        return $this->morphMany(UserRole::class, 'roleable');
    }
}
