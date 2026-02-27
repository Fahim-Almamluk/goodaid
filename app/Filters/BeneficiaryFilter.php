<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class BeneficiaryFilter
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
            ->when($this->get('beneficiary_id'), fn($q, $id) => $q->where('id', $id))
            ->when($this->get('residence_status'), fn($q, $status) => $q->where('residence_status', $status))
            ->when($this->get('relationship'), fn($q, $rel) => $q->where('relationship', $rel))
            ->when($this->get('status'), fn($q, $status) => $q->where('status', $status))
            ->when($this->get('has_pregnant'), fn($q) => $q->where('has_pregnant', true))
            ->when($this->get('has_nursing'), fn($q) => $q->where('has_nursing', true))
            ->when($this->get('has_children'), fn($q) => $this->filterByChildren($q))
            ->when($this->get('filter_members'), fn($q) => $this->filterByMembers($q));
    }

    protected function get(string $key, $default = null)
    {
        return $this->filters[$key] ?? $default;
    }

    protected function filterByChildren(Builder $query): Builder
    {
        $ageMin = $this->get('age_min');
        $ageMax = $this->get('age_max');

        return $query->whereHas('familyMembers', function ($q) use ($ageMin, $ageMax) {
            if ($ageMin !== null) {
                $q->where('age', '>=', (int) $ageMin);
            }
            if ($ageMax !== null) {
                $q->where('age', '<=', (int) $ageMax);
            }
        });
    }

    protected function filterByMembers(Builder $query): Builder
    {
        $membersMin = $this->get('members_min');
        $membersMax = $this->get('members_max');

        if ($membersMin !== null) {
            $query->has('familyMembers', '>=', (int) $membersMin);
        }
        if ($membersMax !== null) {
            $query->has('familyMembers', '<=', (int) $membersMax);
        }

        return $query;
    }
}
