<?php

namespace JosKolenberg\Jory;

use Illuminate\Database\Eloquent\Builder;
use JosKolenberg\Jory\Contracts\JoryInterface;
use JosKolenberg\Jory\Support\Filter;

abstract class QueryBuilder
{

    protected $jory;

    public function __construct(JoryInterface $jory)
    {
        $this->jory = $jory;
    }

    public function query()
    {
        return $this->buildQuery();
    }

    protected function buildQuery()
    {
        $query = clone $this->getBaseQuery();

        $this->applyFilters($query);

        return $query;
    }

    protected function applyFilters(Builder $query)
    {
        foreach ($this->jory->getFilters() as $filter){
            $this->applySingleFilter($query, $filter);
        }
    }

    protected function applySingleFilter(Builder $query, Filter $filter)
    {
        $customMethod = 'apply' . studly_case($filter->getField()) . 'Filter';
        $method = method_exists($this, $customMethod) ? $customMethod : 'doApplyDefaultFilter';
        $this->$method($query, $filter);
    }

    protected function doApplyDefaultFilter(Builder $query, Filter $filter)
    {
        $query->where($filter->getField(), $filter->getOperator(), $filter->getValue());
    }

    abstract protected function getBaseQuery();
}