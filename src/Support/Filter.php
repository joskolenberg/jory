<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 20:59
 */

namespace JosKolenberg\Jory\Support;


use JosKolenberg\Jory\Contracts\FilterInterface;

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
    /**
     * @var null
     */
    private $additional;

    public function __construct(string $field, string $operator, string $value, $additional=null)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
        $this->additional = $additional;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return null
     */
    public function getAdditional()
    {
        return $this->additional;
    }
}