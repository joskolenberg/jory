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
    protected $field;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var string
     */
    protected $data;

    public function __construct(string $field, string $operator = null, $data = null)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->data = $data;
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
    public function getData()
    {
        return $this->data;
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
            case 'd':
            case 'data':
                return $this->getData();
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
