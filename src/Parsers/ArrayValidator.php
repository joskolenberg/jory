<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Helpers\KeyRepository;

/**
 * Class to validate an array with jory data.
 *
 * Class ArrayValidator
 */
class ArrayValidator
{
    /**
     * @var array
     */
    protected $joryArray;

    /**
     * @var string
     */
    protected $address;

    /**
     * ArrayValidator constructor.
     *
     * @param array $joryArray
     * @param string $address
     */
    public function __construct(array $joryArray, string $address = '')
    {
        $this->joryArray = $joryArray;
        $this->address = $address === '' ? '' : $address.'.'; // postfix with dot
    }

    /**
     * Validate the data given in the constructor
     * Throws a JoryException on failure.
     *
     * @throws JoryException on failure
     */
    public function validate(): void
    {
        $this->validateRootObject();
        $this->validateRootFilter();
        $this->validateRelations();
        $this->validateSorts();
        $this->validateOffset();
        $this->validateLimit();
        $this->validateFields();
    }

    /**
     * Validate the filter part of the array.
     *
     * @throws JoryException
     */
    protected function validateRootFilter(): void
    {
        $rootFilter = $this->getArrayValue($this->joryArray, ['flt', 'filter']);

        // It's a string, that's ok because it will be converted to a field-only-filter by the parser later.
        if (is_string($rootFilter)) {
            return;
        }

        // It is not required to add a filter, the absence of a filter just means: don't apply a filter.
        // An empty array also counts as no filter.
        // And if no filter is present, there can be no errors; so return.
        if ($rootFilter === null || count($rootFilter) === 0) {
            return;
        }

        // There is a filter, loop through all filters
        $this->validateFilter($rootFilter, $this->address.'filter');
    }

    /**
     * Validate if the root of the Jory Query contains any unknown keys.
     *
     * @throws JoryException
     */
    protected function validateRootObject(): void
    {
        $this->validateUnknownKeys($this->joryArray, [
            'fld',
            'srt',
            'flt',
            'rlt',
            'ofs',
            'lmt',
        ], $this->address . 'root');
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
     * Tell if one of the given keys exists in array.
     *
     * @param array $array
     * @param array $keys
     *
     * @return bool
     */
    protected function hasArrayKey(array $array, array $keys): bool
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $array)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate a single filter
     * Throws a JoryException on failure.
     *
     * @param array $filter
     * @param $address
     *
     * @throws JoryException
     */
    protected function validateFilter(array $filter, $address): void
    {
        $this->validateUnknownKeys($filter, [
            'f',
            'o',
            'd',
            'and',
            'or',
        ], $address);

        $foundKeys = [];
        foreach (['f', 'field', 'and', 'group_and', 'or', 'group_or'] as $key) {
            if (array_key_exists($key, $filter)) {
                $foundKeys[] = $key;
            }
        }

        if (count($foundKeys) === 0) {
            throw new JoryException('A filter should contain one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: '.$address.')');
        }
        if (count($foundKeys) > 1) {
            throw new JoryException('A filter cannot contain more than one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: '.$address.')');
        }
        $foundKey = $foundKeys[0];

        if ($foundKey === 'f' || $foundKey === 'field') {
            // This is an actual filter, this means:

            // It is not allowed to have both an normal and minimized key, this could result in confusion
            if (array_key_exists('o', $filter) && array_key_exists('operator', $filter)) {
                throw new JoryException('A filter cannot contain both an "o" and "operator" parameter, remove one. (Location: '.$address.')');
            }
            if (array_key_exists('d', $filter) && array_key_exists('data', $filter)) {
                throw new JoryException('A filter cannot contain both an "d" and "data" parameter, remove one. (Location: '.$address.')');
            }
            // A string value in "field" (or "f") is required
            if (! is_string($filter[$foundKey])) {
                throw new JoryException('The "'.$foundKey.'" parameter should have a string value. (Location: '.$address.')');
            }
            // A string value in "operator" (or "o") is optional
            if ($this->hasArrayKey($filter, ['o', 'operator']) && ! is_string($this->getArrayValue($filter, [
                    'o',
                    'operator',
                ]))) {
                throw new JoryException('The "operator" (or "o") parameter should have a string value or be omitted. (Location: '.$address.')');
            }
            // A mixed value in "data" is optional
            // No checks needed here

            // If extra fields are present; they will be omitted by the parser
        }
        if ($foundKey === 'and' || $foundKey === 'group_and') {
            // This is a group_and filter, this means:

            // This filter should contain an array with valid subfilters
            $subFilters = $filter[$foundKey];
            if (! is_array($subFilters)) {
                throw new JoryException('The "'.$foundKey.'" parameter should hold an array with filters. (Location: '.$address.')');
            }
            foreach ($subFilters as $key => $subFilter) {
                $this->validateFilter($subFilter, $address.'(and).'.$key);
            }
        }
        if ($foundKey === 'or' || $foundKey === 'group_or') {
            // This is a group_or filter, this means:

            // This filter should contain an array with valid subfilters
            $subFilters = $filter[$foundKey];
            if (! is_array($subFilters)) {
                throw new JoryException('The "'.$foundKey.'" parameter should hold an array with filters. (Location: '.$address.')');
            }
            foreach ($subFilters as $key => $subFilter) {
                $this->validateFilter($subFilter, $address.'(or).'.$key);
            }
        }
    }

    /**
     * Validate the relations
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateRelations(): void
    {
        $relations = $this->getArrayValue($this->joryArray, ['rlt', 'relations']);

        // No relations set, that's ok. return.
        if (! $relations) {
            return;
        }

        if (! is_array($relations)) {
            throw new JoryException('The relation parameter should be an array. (Location: '.$this->address.'relations)');
        }

        foreach ($relations as $name => $jory) {
            $this->validateRelation($name, $jory);
        }
    }

    /**
     * Validate a single relation
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateRelation($name, $jory): void
    {
        if (empty($name)) {
            throw new JoryException('A relations name should not be empty. (Location: '.$this->address.'relations)');
        }
        // The data in $jory is another jory array, validate recursive with new validator.
        (new self($jory, $this->address.$name))->validate();
    }

    /**
     * Validate the sorts
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateSorts(): void
    {
        $sorts = $this->getArrayValue($this->joryArray, ['srt', 'sorts']);

        // No sorts set, that's ok. return.
        if (! $sorts) {
            return;
        }

        // It's a string, that's ok because it will be converted to an single-item-array by the parser later.
        if (is_string($sorts)) {
            return;
        }

        if (! is_array($sorts)) {
            throw new JoryException('The sorts parameter should be an array or string. (Location: '.$this->address.'sorts)');
        }

        foreach ($sorts as $sort) {
            if (! is_string($sort)) {
                throw new JoryException('A sort item must be a string. (Location: '.$this->address.'sorts)');
            }
        }
    }

    /**
     * Validate the offset value
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateOffset(): void
    {
        $offset = $this->getArrayValue($this->joryArray, ['ofs', 'offset']);

        // No offset set, that's ok. return.
        if ($offset === null) {
            return;
        }

        if (! is_int($offset)) {
            throw new JoryException('The offset parameter should be an integer value. (Location: '.$this->address.'offset)');
        }
    }

    /**
     * Validate the limit value
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateLimit(): void
    {
        $limit = $this->getArrayValue($this->joryArray, ['lmt', 'limit']);

        // No limit set, that's ok. return.
        if ($limit === null) {
            return;
        }

        if (! is_int($limit)) {
            throw new JoryException('The limit parameter should be an integer value. (Location: '.$this->address.'limit)');
        }
    }

    /**
     * Validate the fields
     * Throws a JoryException on failure.
     *
     * @throws JoryException
     */
    protected function validateFields(): void
    {
        $fields = $this->getArrayValue($this->joryArray, ['fld', 'fields']);

        // No fields set, that's ok. return.
        if ($fields === null) {
            return;
        }

        // It's a string, that's ok because it will be converted to an single-item-array by the parser later.
        if (is_string($fields)) {
            return;
        }

        if (! is_array($fields)) {
            throw new JoryException('The fields parameter must be an array or string. (Location: '.$this->address.'fields)');
        }

        foreach ($fields as $key => $field) {
            if (! is_string($field)) {
                throw new JoryException('The fields parameter can only contain strings. (Location: '.$this->address.'fields.'.$key.')');
            }
        }
    }

    /**
     * Check if the input array contains any other keys than the allowed keys.
     *
     * @param array $input
     * @param array $allowedMinifiedKeys
     * @param string $address
     * @throws JoryException
     */
    protected function validateUnknownKeys(array $input, array $allowedMinifiedKeys, string $address)
    {
        $keyRepository = new KeyRepository();

        $allowedKeys = [];
        foreach ($allowedMinifiedKeys as $key){
            $allowedKeys[] = $key;
            $allowedKeys[] = $keyRepository->getFull($key);
        }

        foreach ($input as $key => $item){
            if(!in_array($key, $allowedKeys)){
                throw new JoryException('Unknown key "' . $key . '" in Jory Query. (Location: '.$address.')');
            }
        }
    }
}
