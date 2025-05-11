<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cursus extends Model
{
    use HasFactory;

    protected $table = 'cursus';

    protected $fillable = [
        'name',
        'progression',
        'school_id',
    ];

    /**
     * Get the school that owns the cursus.
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Get the levels for the cursus.
     */
    public function levels(): HasMany
    {
        return $this->hasMany(CursusLevel::class)->orderBy('order');
    }

    /**
     * Get the classes for the cursus.
     */
    public function classrooms(): HasMany
    {
        return $this->hasMany(Classroom::class);
    }
}
