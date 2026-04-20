<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'relationship_to_child',
        'contact_number',
        'address',
    ];

    /**
     * Get the user account associated with the guardian.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the students associated with the guardian.
     * Changed from BelongsToMany to HasMany to match the migration schema.
     */
    public function students(): HasMany
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }

    /**
     * Helper to get the full name of the guardian.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
