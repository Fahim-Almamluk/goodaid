<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class BatchFilter
{
    protected Builder $query;
    protected array $filters;

    public function __construct(Builder $query, array $filters)
    {
        $this->query = $query;
        $this->filters = $filters;
    }

    public function apply(): Builder
    {
        return $this->query
            ->when($this->get('search'), function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                          ->orWhere('type', 'like', "%{$search}%");
                });
            })
            ->when($this->get('status'), fn($q, $status) => $q->where('status', $status));
    }

    protected function get(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }
}
