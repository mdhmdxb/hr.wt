<?php

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Collection;

/**
 * Fake relation for FileBasedOwner: unreadNotifications(), notifications().
 * Supports ->take(n)->get(), ->count(), ->where()->first(), ->paginate().
 */
class OwnerEmptyRelation
{
    public function take(int $value): self
    {
        return $this;
    }

    public function where(string $key, $value): self
    {
        return $this;
    }

    public function get(): Collection
    {
        return new Collection([]);
    }

    public function count(): int
    {
        return 0;
    }

    public function first(): mixed
    {
        return null;
    }

    public function paginate(int $perPage = 15): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $perPage);
    }

    /** No-op for markAsRead on empty relation. */
    public function markAsRead(): void
    {
    }
}
