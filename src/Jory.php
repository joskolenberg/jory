<?php

namespace JosKolenberg\Jory;

use JosKolenberg\Jory\Support\Sort;
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
     * @var array
     */
    protected $sorts = [];

    /**
     * @var null|int
     */
    protected $offset = null;

    /**
     * @var null|int
     */
    protected $limit = null;

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
     * Add a sort.
     *
     * @param Sort $sort
     */
    public function addSort(Sort $sort): void
    {
        $this->sorts[] = $sort;
    }

    /**
     * Get the sorts.
     *
     * @return array
     */
    public function getSorts(): array
    {
        return $this->sorts;
    }

    /**
     * Get the offset value.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * Set the offset value.
     *
     * @param int|null $offset
     */
    public function setOffset(?int $offset): void
    {
        $this->offset = $offset;
    }


    /**
     * Get the limit value.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Set the limit value.
     *
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
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
