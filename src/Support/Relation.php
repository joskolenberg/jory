<?php

namespace JosKolenberg\Jory\Support;


use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Jory;

/**
 * Class to hold data for a relation
 *
 * Class Relation
 * @package JosKolenberg\Jory\Support
 */
class Relation
{

    /**
     * @var string
     */
    protected $relation;

    /**
     * @var Jory|null
     */
    protected $jory;

    /**
     * @var string|null
     */
    protected $alias;

    /**
     * Relation constructor.
     *
     * @param string $relation Name of the relation.
     * @param Jory|null $jory Jory object for querying the relation.
     * @param string|null $alias Alias name for returning the relation on another key.
     * @throws JoryException
     */
    public function __construct(string $relation, Jory $jory = null, string $alias = null)
    {
        if(empty($relation)) throw new JoryException('A relation name cannot be empty.');

        $this->relation = $relation;
        $this->jory = $jory;

        // Empty string resolves to no alias
        $alias = $alias ? $alias : null;

        // When alias name equals the relation name it is considered no alias
        if($alias == $relation) $alias = null;
        $this->alias = $alias;
    }

    /**
     * Get the name of the relation to be retrieved.
     *
     * @return string
     */
    public function getRelation(): string
    {
        return $this->relation;
    }

    /**
     * Get the Jory object for querying the relation.
     *
     * @return Jory
     */
    public function getJory():? Jory
    {
        return $this->jory;
    }

    /**
     * Get the alias for he relation if it should be returned on another key than the relations name.
     *
     * @return string
     */
    public function getAlias():? string
    {
        return $this->alias;
    }

    /**
     * Magic method for accessing attributes.
     *
     * @param $attribute
     *
     * @return Jory|null|string
     */
    public function __get($attribute)
    {
        switch ($attribute) {
            case 'r':
            case 'relation':
                return $this->getRelation();
            case 'j':
            case 'jory':
                return $this->getJory();
            case 'a':
            case 'alias':
                return $this->getAlias();
        }
    }
}