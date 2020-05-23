<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Sort;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
use JosKolenberg\Jory\Helpers\KeyRepository;
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
     * @var \JosKolenberg\Jory\Helpers\KeyRepository
     */
    protected $keyRepository;

    /**
     * ArrayParser constructor.
     *
     * @param array $joryArray
     */
    public function __construct(array $joryArray)
    {
        $this->joryArray = $joryArray;
        $this->keyRepository = new KeyRepository();
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
        $data = $this->getArrayValue($this->joryArray, 'flt');
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
        // If input is a string we convert it to a simple filter with only a field defined.
        if(is_string($data)){
            return new Filter($data);
        }

        if (($groupAndData = $this->getArrayValue($data, 'and')) !== null) {
            $group = new GroupAndFilter();
            foreach ($groupAndData as $filter) {
                $group->push($this->getFilterFromData($filter));
            }

            return $group;
        }
        if (($groupOrData = $this->getArrayValue($data, 'or')) !== null) {
            $group = new GroupOrFilter();
            foreach ($groupOrData as $filter) {
                $group->push($this->getFilterFromData($filter));
            }

            return $group;
        }

        // No group filter; must be a regular Filter
        $field = $this->getArrayValue($data, 'f');
        return new Filter($field, $this->getArrayValue($data, 'o'), $this->getArrayValue($data, 'd'));
    }

    /**
     * Get value from array based on multiple keys.
     *
     * @param array $array
     * @param string $key
     *
     * @return mixed|null
     */
    protected function getArrayValue(array $array, string $key)
    {
        return $this->keyRepository->getArrayValue($array, $key);
    }

    /**
     * Set the relations on the jory object based on the given data in constructor.
     *
     * @param Jory $jory
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    protected function setRelations(Jory $jory): void
    {
        $relations = $this->getArrayValue($this->joryArray, 'rlt');

        if ($relations) {
            $relations = $this->convertDotNotatedRelations($relations);
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
        $sorts = $this->getArrayValue($this->joryArray, 'srt');

        if(is_string($sorts)){
            $sorts = [$sorts];
        }

        if ($sorts) {
            foreach ($sorts as $sort) {
                $order = 'asc';
                if (substr($sort, 0, 1) === '-') {
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
        $offset = $this->getArrayValue($this->joryArray, 'ofs');
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
        $limit = $this->getArrayValue($this->joryArray, 'lmt');
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
        $fields = $this->getArrayValue($this->joryArray, 'fld');

        if(!$fields){
            return;
        }

        if(is_string($fields)){
            $fields = [$fields];
        }

        $jory->setFields($fields);
    }

    /**
     * Convert relations which are written in dot-notation to subrelations in the relations array.
     *
     * @param $relations
     * @return mixed
     */
    protected function convertDotNotatedRelations($relations)
    {
        $dottedRelations = [];
        foreach ($relations as $name => $joryData) {
            $exploded = explode('.', $name);

            if (count($exploded) > 1) {
                // There was a dot, add it to the subRelations
                $firstRelation = $exploded[0];
                unset($exploded[0]);

                // implode all next layers, they will be handled in the next parser
                $dottedRelations[$firstRelation][implode('.', $exploded)] = $joryData;

                unset($relations[$name]);
            }
        }

        foreach ($dottedRelations as $name => $subRelations) {
            if (!array_key_exists($name, $relations)) {
                // This relation doesn't already exists, create it
                $relations[$name] = ['rlt' => []];
            }

            foreach ($subRelations as $subName => $joryData) {
                if (array_key_exists('rlt', $relations[$name])) {
                    $relations[$name]['rlt'][$subName] = $joryData;
                } else {
                    $relations[$name]['relations'][$subName] = $joryData;
                }
            }
        }

        return $relations;
    }
}
