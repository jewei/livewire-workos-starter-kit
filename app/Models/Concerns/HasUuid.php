<?php

declare(strict_types=1);

namespace App\Models\Concerns;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUniqueStringIds;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Override;

trait HasUuid
{
    use HasUniqueStringIds;

    /**
     * Find a model by its UUID.
     */
    public static function findByUuid(string $uuid): ?Model
    {
        return self::where('uuid', $uuid)->first();
    }

    /**
     * Generate a new unique key for the model.
     */
    public function newUniqueId(): string
    {
        return (string) Str::uuid7();
    }

    /**
     * Determine if given key is valid.
     */
    protected function isValidUniqueId(mixed $value): bool
    {
        return Str::isUuid($value);
    }

    /**
     * Initialize the trait.
     */
    public function initializeHasUniqueStringIds(): void
    {
        $this->usesUniqueIds = true;
    }

    #[Override]
    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array<int, string>
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    #[Override]
    /**
     * Retrieve the model for a bound value.
     */
    public function resolveRouteBindingQuery($query, $value, $field = null): Builder
    {
        if (! $this->isValidUniqueId($value)) {
            throw (new ModelNotFoundException)->setModel($this::class, $value);
        }

        return $query->where('uuid', $value);
    }

    #[Override]
    /**
     * Get the auto-incrementing key type.
     */
    public function getKeyType(): string
    {
        return 'int';
    }

    #[Override]
    /**
     * Get the value indicating whether the IDs are incrementing.
     */
    public function getIncrementing(): bool
    {
        return true;
    }

    #[Override]
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
