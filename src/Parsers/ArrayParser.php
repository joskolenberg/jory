<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Sort;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Contracts\JoryParserInterface;

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
        $this->joryArray = $joryArray;
    }

    /**
     * Get the Jory object based on the given data.
     *
     * @return Jory
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function getJory(): Jory
    {
        (new ArrayValidator($this->joryArray))->validate();

        $jory = new Jory();
        $this->setFilters($jory);
        $this->setRelations($jory);
        $this->setSorts($jory);
        $this->setOffset($jory);
        $this->setLimit($jory);
        $this->setFields($jory);

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
            return new Filter($field, $this->getArrayValue($data, ['o', 'operator']), $this->getArrayValue($data, [
                'd',
                'data',
            ]));
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
            foreach ($sorts as $sort) {
                $order = 'asc';
                if(substr($sort, 0, 1) === '-'){
                    $order = 'desc';
                    $sort = substr($sort, 1);
                }
                try {
                    $jory->addSort(new Sort($sort, $order));
                } catch (JoryException $e) {
                    // This exception cannot be thrown here because we checked the data.
                    // Use empty try/catch to prevent IDE suggesting we should use a @throws tag.
                }
            }
        }
    }

    /**
     * Set the offset on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setOffset(Jory $jory): void
    {
        $offset = $this->getArrayValue($this->joryArray, ['ofs', 'offset']);
        if ($offset !== null) {
            $jory->setOffset($offset);
        }
    }

    /**
     * Set the limit on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setLimit(Jory $jory): void
    {
        $limit = $this->getArrayValue($this->joryArray, ['lmt', 'limit']);
        if ($limit !== null) {
            $jory->setLimit($limit);
        }
    }

    /**
     * Set the fields on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     */
    protected function setFields(Jory $jory): void
    {
        $fields = $this->getArrayValue($this->joryArray, ['fld', 'fields']);

        $jory->setFields($fields);
    }
}
