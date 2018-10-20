<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
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
     * @var bool
     */
    protected $minified;

    /**
     * ToArrayConverter constructor.
     *
     * @param Jory $jory
     * @param bool $minified
     */
    public function __construct(Jory $jory, bool $minified = true)
    {
        $this->jory = $jory;
        $this->minified = $minified;
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
            $result[$this->minified ? 'flt' : 'filter'] = $this->getFilterArray($filter);
        }
        $sorts = $this->jory->getSorts();
        if ($sorts) {
            $result[$this->minified ? 'srt' : 'sorts'] = $this->getSortsArray($sorts);
        }
        $relations = $this->jory->getRelations();
        if ($relations) {
            $result[$this->minified ? 'rlt' : 'relations'] = $this->getRelationsArray($relations);
        }
        if($this->jory->getOffset() != null){
            $result[$this->minified ? 'ofs' : 'offset'] = $this->jory->getOffset();
        }
        if($this->jory->getLimit() != null){
            $result[$this->minified ? 'lmt' : 'limit'] = $this->jory->getLimit();
        }

        return $result;
    }

    /**
     * Get the filter part of the array.
     *
     * @param FilterInterface $filter
     *
     * @return mixed
     */
    protected function getFilterArray(FilterInterface $filter): array
    {
        if ($filter instanceof Filter) {
            $result[$this->minified ? 'f' : 'field'] = $filter->getField();
            if ($filter->getOperator() !== null) {
                $result[$this->minified ? 'o' : 'operator'] = $filter->getOperator();
            }
            if ($filter->getValue() !== null) {
                $result[$this->minified ? 'v' : 'value'] = $filter->getValue();
            }

            return $result;
        }
        if ($filter instanceof GroupAndFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->minified ? 'and' : 'group_and'] = $group;

            return $result;
        }
        if ($filter instanceof GroupOrFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->minified ? 'or' : 'group_or'] = $group;

            return $result;
        }
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

            $relationsArray[$key] = (new self($relation->getJory(), $this->minified))->get();
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
            $sortsArray[$sort->getField()] = $sort->getOrder();
        }

        return $sortsArray;
    }
}
