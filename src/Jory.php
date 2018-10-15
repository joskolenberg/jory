<?php

namespace JosKolenberg\Jory;

use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\Relation;
use JosKolenberg\Jory\Contracts\FilterInterface;
use JosKolenberg\Jory\Converters\ToJsonConverter;
use JosKolenberg\Jory\Converters\ToArrayConverter;

/**
 * Class to hold Jory data which can be used to modify database queries.
 *
 * Class Jory
 */
class Jory
{
    /**
     * @var FilterInterface|null
     */
    protected $filter;

    /**
     * @var array
     */
    protected $relations = [];

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
     * Add a relation.
     *
     * @param Relation $relation
     */
    public function addRelation(Relation $relation): void
    {
        $this->relations[] = $relation;
    }

    /**
     * Get the relations.
     *
     * @return array
     */
    public function getRelations(): array
    {
        return $this->relations;
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
