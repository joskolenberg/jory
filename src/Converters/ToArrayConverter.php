<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Helpers\KeyRespository;
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
     * @var bool
     */
    protected $minified;

    /**
     * @var \JosKolenberg\Jory\Helpers\KeyRespository
     */
    protected $keys;

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
        $this->keys = (new KeyRespository())->minified($minified);
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
            $result[$this->keys->get('flt')] = $this->getFilterArray($filter);
        }
        $sorts = $this->jory->getSorts();
        if ($sorts) {
            $result[$this->keys->get('srt')] = $this->getSortsArray($sorts);
        }
        $relations = $this->jory->getRelations();
        if ($relations) {
            $result[$this->keys->get('rlt')] = $this->getRelationsArray($relations);
        }
        if ($this->jory->getOffset() !== null) {
            $result[$this->keys->get('ofs')] = $this->jory->getOffset();
        }
        if ($this->jory->getLimit() !== null) {
            $result[$this->keys->get('lmt')] = $this->jory->getLimit();
        }
        if ($this->jory->getFields() !== null) {
            $result[$this->keys->get('fld')] = $this->jory->getFields();
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
            $result[$this->keys->get('f')] = $filter->getField();
            if ($filter->getOperator() !== null) {
                $result[$this->keys->get('o')] = $filter->getOperator();
            }
            if ($filter->getData() !== null) {
                $result[$this->keys->get('d')] = $filter->getData();
            }

            return $result;
        }
        if ($filter instanceof GroupAndFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->keys->get('and')] = $group;

            return $result;
        }
        if ($filter instanceof GroupOrFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->keys->get('or')] = $group;

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
            $sortsArray[] = ($sort->getOrder() === 'desc' ? '-' : '').$sort->getField();
        }

        return $sortsArray;
    }
}
