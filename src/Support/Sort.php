<?php

namespace JosKolenberg\Jory\Support;


use JosKolenberg\Jory\Exceptions\JoryException;

/**
 * Class to hold data by which a query can be sorted.
 *
 * @package JosKolenberg\Jory\Support
 */
class Sort
{

    /**
     * @var string
     */
    protected $field;
    /**
     * @var string
     */
    protected $order;

    /**
     * Sort constructor.
     *
     * @param string $field
     * @param string $order
     *
     * @throws JoryException
     */
    public function __construct(string $field, string $order='asc')
    {
        if(!in_array($order, ['asc', 'desc'])) throw new JoryException('A sorts order can only be asc or desc.');

        $this->field = $field;
        $this->order = $order;
    }

    /**
     * Get the field to sort on.
     *
     * @return string
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Get the order in which the data should be sorted (asc or desc).
     *
     * @return string
     */
    public function getOrder(): string
    {
        return $this->order;
    }

    /**
     * Magic method for accessing attributes.
     *
     * @param $attribute
     *
     * @return null|string
     */
    public function __get($attribute)
    {
        switch ($attribute) {
            case 'f':
            case 'field':
                return $this->getField();
            case 'o':
            case 'order':
                return $this->getOrder();
        }
    }
}