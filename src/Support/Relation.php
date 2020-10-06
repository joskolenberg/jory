<?php

namespace JosKolenberg\Jory\Support;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Exceptions\JoryException;

/**
 * Class to hold data for a relation.
 *
 * Class Relation
 */
class Relation
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Jory|null
     */
    protected $jory;

    /**
     * Relation constructor.
     *
     * @param string $name Name of the relation.
     * @param Jory|null $jory Jory object for querying the relation.
     *
     * @throws JoryException
     */
    public function __construct(string $name, Jory $jory = null)
    {
        if (empty($name)) {
            throw new JoryException('A relation name cannot be empty.');
        }
        $this->name = $name;
        $this->jory = $jory;
    }

    /**
     * Get the name of the relation to be retrieved.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the Jory object for querying the relation.
     *
     * @return Jory
     */
    public function getJory(): ? Jory
    {
        return $this->jory;
    }
}
