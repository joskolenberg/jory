<?php

namespace JosKolenberg\Jory;

use Illuminate\Database\Query\Builder;
use JosKolenberg\Jory\Contracts\JoryInterface;

class QueryBuilder
{

    protected $builder;
    protected $jory;

    public function __construct(Builder $builder, JoryInterface $jory)
    {
        $this->builder = $builder;
        $this->jory = $jory;
    }

    public function setJory(JoryInterface $jory)
    {
        $this->jory = $jory;
    }

    public function query()
    {
        return $this->buildQuery();
    }

    protected function buildQuery()
    {
        $query = clone $this->builder;

        $this->applyFilters($query);
//        $this->applySorts($query);
//        $this->applyFields($query);
//        $this->applyRelations($query);

        return $query;
    }

    protected function applyFilters(Builder $query)
    {
        foreach ($this->jory->getFilters() as $filter){
            $this->applySingleFilter($query, $filter);
        }
    }

//    protected function applySorts(Builder $query)
//    {
//    }
//
//    protected function applyFields(Builder $query)
//    {
//    }
//
//    protected function applyRelations(Builder $query)
//    {
//    }
//
    protected function applySingleFilter(Builder $query, $filter)
    {
        $customMethod = 'apply' . studly_case($filter->field) . 'Filter';
        $method = method_exists($this, $customMethod) ? $customMethod : 'doApplyDefaultFilter';
        $this->$method($query, $filter);
    }

    protected function doApplyDefaultFilter(Builder $query, $filter)
    {
        $query->where($filter->field, $filter->operator, $filter->value);
    }
}