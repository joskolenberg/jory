<?php

namespace JosKolenberg\Jory\Contracts;

/**
 * Interface FilterInterface.
 */
interface FilterInterface
{

    /**
     * Tell if this filter contains a filter on the given field.
     *
     * @param string $field
     * @return bool
     */
    public function hasFilter(string $field): bool;
}
