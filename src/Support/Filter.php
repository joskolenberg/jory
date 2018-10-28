<?php

namespace JosKolenberg\Jory\Support;

use JosKolenberg\Jory\Contracts\FilterInterface;

/**
 * Class to hold data for a single Jory filter.
 *
 * Class Filter
 */
class Filter implements FilterInterface
{
    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    public function __construct(string $field, string $operator = null, $value = null)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator(): ? string
    {
        return $this->operator;
    }

    /**
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Magic method for accessing attributes.
     *
     * @param $attribute
     *
     * @return mixed|null|string
     */
    public function __get($attribute)
    {
        switch ($attribute) {
            case 'f':
            case 'field':
                return $this->getField();
            case 'o':
            case 'operator':
                return $this->getOperator();
            case 'v':
            case 'value':
                return $this->getValue();
        }
    }

    /**
     * Tell if this filter contains a filter on the given field.
     *
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool
    {
        return $this->field === $field;
    }
}
