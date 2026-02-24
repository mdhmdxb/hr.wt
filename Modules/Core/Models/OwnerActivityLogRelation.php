<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Collection;

/**
 * Fake relation for FileBasedOwner so activityLogs() returns an empty result set.
 */
class OwnerActivityLogRelation
{
    public function exists(): bool
    {
        return false;
    }

    public function latest(string $column = null): self
    {
        return $this;
    }

    public function take(int $value): self
    {
        return $this;
    }

    /** @return Collection<int, ActivityLog> */
    public function get(): Collection
    {
        return new Collection([]);
    }
}
