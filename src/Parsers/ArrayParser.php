<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Support\Sort;

/**
 * Class to parse an array with associative jory data to an Jory object.
 *
 * Class ArrayParser
 */
class ArrayParser implements JoryParserInterface
{
    /**
     * @var array
     */
    protected $joryArray;

    /**
     * ArrayParser constructor.
     *
     * @param array $joryArray
     */
    public function __construct(array $joryArray)
    {
        (new ArrayValidator($joryArray))->validate();

        $this->joryArray = $joryArray;
    }

    /**
     * Get the Jory object based on the given data.
     *
     * @return Jory
     */
    public function getJory(): Jory
    {
        $jory = new Jory();
        $this->setFilters($jory);
        $this->setRelations($jory);
        $this->setSorts($jory);

        return $jory;
    }

    /**
     * Set the filters on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setFilters(Jory $jory): void
    {
        $data = $this->getArrayValue($this->joryArray, ['flt', 'filter']);
        if ($data) {
            $jory->setFilter($this->getFilterFromData($data));
        }
    }

    /**
     * Get a single filter based on $data parameter.
     *
     * @param $data
     *
     * @return FilterInterface
     */
    protected function getFilterFromData($data): FilterInterface
    {
        if (($field = $this->getArrayValue($data, ['f', 'field'])) !== null) {
            return new Filter($field,
                $this->getArrayValue($data, ['o', 'operator']),
                $this->getArrayValue($data, ['v', 'value']));
        }
        if (($groupAndData = $this->getArrayValue($data, ['and', 'group_and'])) !== null) {
            $group = new GroupAndFilter();
            foreach ($groupAndData as $filter) {
                $group->push($this->getFilterFromData($filter));
            }

            return $group;
        }
        if (($groupOrData = $this->getArrayValue($data, ['or', 'group_or'])) !== null) {
            $group = new GroupOrFilter();
            foreach ($groupOrData as $filter) {
                $group->push($this->getFilterFromData($filter));
            }

            return $group;
        }
    }

    /**
     * Get value from array based on multiple keys.
     *
     * @param array $array
     * @param array $keys
     *
     * @return mixed|null
     */
    protected function getArrayValue(array $array, array $keys)
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return $array[$key];
            }
        }
    }

    /**
     * Set the relations on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setRelations(Jory $jory): void
    {
        $relations = $this->getArrayValue($this->joryArray, ['rlt', 'relations']);

        if ($relations) {
            foreach ($relations as $name => $joryData) {
                $subJory = (new self($joryData))->getJory();
                $jory->addRelation(new Relation($name, $subJory));
            }
        }
    }

    /**
     * Set the sorts on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setSorts(Jory $jory): void
    {
        $sorts = $this->getArrayValue($this->joryArray, ['srt', 'sorts']);

        if ($sorts) {
            foreach ($sorts as $field => $order) {
                $jory->addSort(new Sort($field, $order));
            }
        }
    }
}
