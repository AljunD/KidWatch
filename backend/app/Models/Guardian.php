<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guardian extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'middle_name',
        'last_name',
        'relationship_to_child',
        'contact_number',
        'address',
        'trashed_at',
        'deleted_at',
    ];

    protected $dates = [
        'trashed_at',
        'deleted_at',
        'created_at',
        'updated_at',
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

    /**
     * Soft delete (move to trash).
     */
    public function trash(): void
    {
        $this->trashed_at = now();
        $this->save();
    }

    /**
     * Restore from trash.
     */
    public function restoreFromTrash(): void
    {
        $this->trashed_at = null;
        $this->save();
    }

    /**
     * Hard delete (permanent removal).
     */
    public function hardDelete(): void
    {
        $this->deleted_at = now();
        $this->save();
        parent::delete(); // permanently remove from DB
    }
}
