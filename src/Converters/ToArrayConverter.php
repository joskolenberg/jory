<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Helpers\KeyRepository;
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
     * @var \JosKolenberg\Jory\Helpers\KeyRepository
     */
    protected $keyRepository;

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
        $this->keyRepository = (new KeyRepository())->minified($minified);
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
            $result[$this->keyRepository->get('flt')] = $this->getFilterArray($filter);
        }
        $sorts = $this->jory->getSorts();
        if (!empty($sorts)) {
            $result[$this->keyRepository->get('srt')] = $this->getSortsArray($sorts);
        }
        $relations = $this->jory->getRelations();
        if (!empty($relations)) {
            $result[$this->keyRepository->get('rlt')] = $this->getRelationsArray($relations);
        }
        if ($this->jory->getOffset() !== null) {
            $result[$this->keyRepository->get('ofs')] = $this->jory->getOffset();
        }
        if ($this->jory->getLimit() !== null) {
            $result[$this->keyRepository->get('lmt')] = $this->jory->getLimit();
        }
        if ($this->jory->getFields() !== null) {
            $result[$this->keyRepository->get('fld')] = $this->jory->getFields();
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
            $result[$this->keyRepository->get('f')] = $filter->getField();
            if ($filter->getOperator() !== null) {
                $result[$this->keyRepository->get('o')] = $filter->getOperator();
            }
            if ($filter->getData() !== null) {
                $result[$this->keyRepository->get('d')] = $filter->getData();
            }
        }
        if ($filter instanceof GroupAndFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->keyRepository->get('and')] = $group;
        }
        if ($filter instanceof GroupOrFilter) {
            $group = [];
            foreach ($filter as $subFilter) {
                $group[] = $this->getFilterArray($subFilter);
            }
            $result[$this->keyRepository->get('or')] = $group;
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
            $sortsArray[] = ($sort->getOrder() === 'desc' ? '-' : '') . $sort->getField();
        }

        return $sortsArray;
    }
}
