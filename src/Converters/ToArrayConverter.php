<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Contracts\FilterInterface;

/**
 * Class to convert a Jory object to an associative array.
 *
 * Class ToArrayConverter
 */
class ToArrayConverter
{
    /**
     * @var Jory
     */
    protected $jory;

    /**
     * ToArrayConverter constructor.
     *
     * @param Jory $jory
     */
    public function __construct(Jory $jory)
    {
        $this->jory = $jory;
    }

    /**
     * Get the array based on given Jory object.
     *
     * @return array
     */
    public function get(): array
    {
        $result = [];

        $filter = $this->jory->getFilter();
        if ($filter !== null) {
            $result['flt'] = $this->getFilterArray($filter);
        }
        $sorts = $this->jory->getSorts();
        if (!empty($sorts)) {
            $result['srt'] = $this->getSortsArray($sorts);
        }
        $relations = $this->jory->getRelations();
        if (!empty($relations)) {
            $result['rlt'] = $this->getRelationsArray($relations);
        }
        if ($this->jory->getOffset() !== null) {
            $result['ofs'] = $this->jory->getOffset();
        }
        if ($this->jory->getLimit() !== null) {
            $result['lmt'] = $this->jory->getLimit();
        }
        if (!empty($this->jory->getFields())) {
            $result['fld'] = $this->jory->getFields();
        }

        return $result;
    }

    /**
     * Get the filter part of the array.
     *
     * @param FilterInterface $filter
     *
     * @return array
     */
    protected function getFilterArray(FilterInterface $filter): array
    {
        $result = [];

        if ($filter instanceof Filter) {
            $result['f'] = $filter->getField();
            if ($filter->getOperator() !== null) {
                $result['o'] = $filter->getOperator();
            }
            if ($filter->getData() !== null) {
                $result['d'] = $filter->getData();
            }
        }
        if ($filter instanceof GroupAndFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result['and'] = $group;
        }
        if ($filter instanceof GroupOrFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result['or'] = $group;
        }

        return $result;
    }

    /**
     * Turn an array of relation objects into an array.
     *
     * @param array $relations
     * @return array
     */
    protected function getRelationsArray(array $relations): array
    {
        $relationsArray = [];
        foreach ($relations as $relation) {
            $key = $relation->getName();

            $relationsArray[$key] = (new self($relation->getJory()))->get();
        }

        return $relationsArray;
    }

    /**
     * Turn an array of sort objects into an array.
     *
     * @param array $sorts
     * @return array
     */
    protected function getSortsArray(array $sorts): array
    {
        $sortsArray = [];
        foreach ($sorts as $sort) {
            $sortsArray[] = ($sort->getOrder() === 'desc' ? '-' : '') . $sort->getField();
        }

        return $sortsArray;
    }
}
