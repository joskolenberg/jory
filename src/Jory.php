<?php

namespace JosKolenberg\Jory;

use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Converters\ToArrayConverter;
use JosKolenberg\Jory\Converters\ToJsonConverter;
use JosKolenberg\Jory\Support\Filter;

/**
 * Class to hold Jory data which can be used to modify database queries.
 *
 * Class Jory
 */
class Jory
{
    /**
     * @var
     */
    protected $filter;

    /**
     * Set the filter.
     *
     * @param FilterInterface $filter
     *
     * @return Jory
     */
    public function setFilter(FilterInterface $filter): self
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Get the filter.
     *
     * @return FilterInterface|null
     */
    public function getFilter():? FilterInterface
    {
        return $this->filter;
    }

    /**
     * Get array export for the Jory object.
     *
     * @param bool $minified
     *
     * @return array
     */
    public function toArray($minified = true): array
    {
        return (new ToArrayConverter($this, $minified))->get();
    }

    /**
     * Get Json export for the jory object.
     *
     * @param bool $minified
     *
     * @return string
     */
    public function toJson($minified = true): string
    {
        return (new ToJsonConverter($this, $minified))->get();
    }
}
