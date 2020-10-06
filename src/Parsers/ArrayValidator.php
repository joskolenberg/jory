<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Exceptions\JoryException;

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
        $rootFilter = $this->getArrayValue($this->joryArray, 'flt');

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
        $this->validateFilter($rootFilter, $this->address.'flt');
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
     * @param string $key
     *
     * @return mixed|null
     */
    protected function getArrayValue(array $array, string $key)
    {
        if (!array_key_exists($key, $array)) {
            return null;
        }

        return $array[$key];
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
        foreach (['f', 'and', 'or'] as $key) {
            if (array_key_exists($key, $filter)) {
                $foundKeys[] = $key;
            }
        }

        if (count($foundKeys) === 0) {
            throw new JoryException('A filter should contain one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: '.$address.')');
        }
        if (count($foundKeys) > 1) {
            throw new JoryException('A filter cannot contain more than one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: '.$address.')');
        }
        $foundKey = $foundKeys[0];

        if ($foundKey === 'f') {
            // This is an actual filter, this means:

            // A field is required
            if (! is_string($filter['f'])) {
                throw new JoryException('The "f" (field) parameter should be a string value. (Location: '.$address.'.f)');
            }
            // An operator is optional
            if (array_key_exists('o', $filter) && ! is_string($this->getArrayValue($filter, 'o'))) {
                throw new JoryException('The "o" (operator) parameter should be a string value or should be omitted. (Location: '.$address.'.o)');
            }
            // A mixed value in the "data" parameter is optional
            // No checks needed here

            // If extra fields are present; they will be cleared by the parser
        }
        if ($foundKey === 'and') {
            // This is an AND group filter, this means:

            // This filter should contain an array with valid subfilters
            $subFilters = $filter['and'];
            if (! is_array($subFilters)) {
                throw new JoryException('The "and" (AND group) parameter should be an array with filters. (Location: '.$address.')');
            }
            foreach ($subFilters as $key => $subFilter) {
                $this->validateFilter($subFilter, $address.'(and).'.$key);
            }
        }
        if ($foundKey === 'or') {
            // This is an OR group filter, this means:

            // This filter should contain an array with valid subfilters
            $subFilters = $filter['or'];
            if (! is_array($subFilters)) {
                throw new JoryException('The "or" (OR group) parameter should be an array with filters. (Location: '.$address.')');
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
        $relations = $this->getArrayValue($this->joryArray, 'rlt');

        // No relations set, that's ok. return.
        if (! $relations) {
            return;
        }

        if (! is_array($relations)) {
            throw new JoryException('The "rlt" (relations) parameter should be an array. (Location: '.$this->address.'relations)');
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
            throw new JoryException('A relation\'s name should not be empty. (Location: '.$this->address.'rlt)');
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
        $sorts = $this->getArrayValue($this->joryArray, 'srt');

        // No sorts set, that's ok. return.
        if (! $sorts) {
            return;
        }

        // It's a string, that's ok because it will be converted to an single-item-array by the parser later.
        if (is_string($sorts)) {
            return;
        }

        if (! is_array($sorts)) {
            throw new JoryException('The "srt" (sorts) parameter should be an array or string. (Location: '.$this->address.'srt)');
        }

        foreach ($sorts as $key => $sort) {
            if (! is_string($sort)) {
                throw new JoryException('A sort item must be a string. (Location: '.$this->address.'srt.' . $key . ')');
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
        $offset = $this->getArrayValue($this->joryArray, 'ofs');

        // No offset set, that's ok. return.
        if ($offset === null) {
            return;
        }

        if (! is_int($offset)) {
            throw new JoryException('The "ofs" (offset) parameter should be an integer value. (Location: '.$this->address.'ofs)');
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
        $limit = $this->getArrayValue($this->joryArray, 'lmt');

        // No limit set, that's ok. return.
        if ($limit === null) {
            return;
        }

        if (! is_int($limit)) {
            throw new JoryException('The "lmt" (limit) parameter should be an integer value. (Location: '.$this->address.'lmt)');
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
        $fields = $this->getArrayValue($this->joryArray, 'fld');

        // No fields set, that's ok. return.
        if (!$fields) {
            return;
        }

        // It's a string, that's ok because it will be converted to an single-item-array by the parser later.
        if (is_string($fields)) {
            return;
        }

        if (! is_array($fields)) {
            throw new JoryException('The "fld" (fields) parameter must be an array or string. (Location: '.$this->address.'fld)');
        }

        foreach ($fields as $key => $field) {
            if (! is_string($field)) {
                throw new JoryException('The "fld" (fields) parameter can only contain strings. (Location: '.$this->address.'fld.'.$key.')');
            }
        }
    }

    /**
     * Check if the input array contains any other keys than the allowed keys.
     *
     * @param array $input
     * @param array $allowedKeys
     * @param string $address
     * @throws JoryException
     */
    protected function validateUnknownKeys(array $input, array $allowedKeys, string $address)
    {
        foreach ($input as $key => $item){
            if(!in_array($key, $allowedKeys)){
                throw new JoryException('Unknown key "' . $key . '" in Jory Query. (Location: '.$address.')');
            }
        }
    }
}
