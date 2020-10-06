<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\Relation;
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

        $this->convertStringedValues();
        $this->convertDotNotatedFields();

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
    protected function getArrayValue(array $array, string $key, $default = null)
    {
        if (!array_key_exists($key, $array)) {
            return $default;
        }

        return $array[$key];
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

        if ($sorts) {
            foreach ($sorts as $sort) {
                $order = 'asc';
                if (substr($sort, 0, 1) === '-') {
                    $order = 'desc';
                    $sort = substr($sort, 1);
                }

                $jory->addSort(new Sort($sort, $order));
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

        if (!$fields) {
            return;
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
                $relations[$name]['rlt'][$subName] = $joryData;
            }
        }

        return $relations;
    }

    protected function convertDotNotatedFields()
    {
        $fields = $this->getArrayValue($this->joryArray, 'fld');

        if (!$fields) {
            return;
        }

        $extractedRelations = [];
        $filteredFields = [];
        foreach ($fields as $field) {
            if (strstr($field, '.') === false) {
                /**
                 * There's no dot, just keep it in the current fields.
                 */
                $filteredFields[] = $field;
                continue;
            }

            /**
             * There's a dot, should be applied to a relation. Store in temporary array with relations,
             * when there are mulitple dots (nested relations) we glue them back
             * together to be handled recursively by the relation's parser.
             */
            $exploded = explode('.', $field);

            $relation = $exploded[0];
            unset($exploded[0]);

            $extractedRelations[$relation][] = implode('.', $exploded);
        }

        /**
         * If there are no relations found there's no need to continue
         * since the filtered fields will be the same as the original.
         */
        if(count($extractedRelations) === 0){
            return;
        }

        /**
         * Unset the original fields and store the filtered fields if there are any left.
         */
        unset($this->joryArray['fld']);
        if (count($filteredFields) > 0) {
            $this->joryArray['fld'] = $filteredFields;
        }

        /**
         * Add the extracted relations to the existing relations in the query.
         */
        $originalRelations = $this->getArrayValue($this->joryArray, 'rlt', []);
        foreach ($extractedRelations as $name => $fields) {
            if (!array_key_exists($name, $originalRelations) || !array_key_exists('fld', $originalRelations[$name])) {
                /**
                 * Relation doesn't exist jet, just add it it.
                 */
                $originalRelations[$name]['fld'] = $fields;
                continue;
            }

            /**
             * The relation already exists, merge the fields with the already defined ones.
             * The fields on the the relation could be a string since convertStringedValues()
             * is not applied to the relations jet, so check that as well.
             */
            $existingFields = $originalRelations[$name]['fld'];

            if(is_string($existingFields)){
                $existingFields = [$existingFields];
            }

            $originalRelations[$name]['fld'] = array_merge($existingFields, $fields);
        }

        $this->joryArray['rlt'] = $originalRelations;
    }

    protected function convertStringedValues()
    {
        /**
         * Convert string values for fields or sorts into a single item array.
         */
        foreach ([
                     'fld',
                     'srt',
                 ] as $type) {
            $data = $this->getArrayValue($this->joryArray, $type);
            if (is_string($data)) {
                $this->joryArray[$type] = [$data];
            }
        }

        /**
         * Convert a string value for a filter to a simple filter with only a field parameter.
         */
        $filter = $this->getArrayValue($this->joryArray, 'flt');
        if (is_string($filter)) {
            $this->joryArray['flt'] = ['f' => $filter];
        }
    }
}
