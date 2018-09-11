<?php

namespace JosKolenberg\Jory\Converters;


use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;

/**
 * Class to convert a Jory object to an associative array
 *
 * Class ToArrayConverter
 * @package JosKolenberg\Jory\Converters
 */
class ToArrayConverter
{
    /**
     * @var Jory
     */
    protected $jory;
    /**
     * @var bool
     */
    protected $minified;

    /**
     * ToArrayConverter constructor.
     * @param Jory $jory
     * @param bool $minified
     */
    public function __construct(Jory $jory, bool $minified = true)
    {
        $this->jory = $jory;
        $this->minified = $minified;
    }

    /**
     * Get the array based on given Jory object
     *
     * @return array
     */
    public function get(): array
    {
        $result = [];

        $filter = $this->jory->getFilter();
        if($filter !== null) $result[$this->minified ? 'flt' : 'filter'] = $this->getFilterArray($filter);

        return $result;
    }

    /**
     * Get the filter part of the array
     *
     * @param FilterInterface $filter
     * @return mixed
     */
    protected function getFilterArray(FilterInterface $filter): array
    {
        if ($filter instanceof Filter) {
            $result[$this->minified ? 'f' : 'field'] = $filter->getField();
            if ($filter->getOperator() !== null) $result[$this->minified ? 'o' : 'operator'] = $filter->getOperator();
            if ($filter->getValue() !== null) $result[$this->minified ? 'v' : 'value'] = $filter->getValue();
            return $result;
        }
        if ($filter instanceof GroupAndFilter) {
            $group = [];
            foreach ($filter as $subFilter){
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->minified ? 'and' : 'group_and'] = $group;
            return $result;
        }
        if ($filter instanceof GroupOrFilter) {
            $group = [];
            foreach ($filter as $subFilter){
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->minified ? 'or' : 'group_or'] = $group;
            return $result;
        }
    }
}