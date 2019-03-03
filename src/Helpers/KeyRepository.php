<?php

namespace JosKolenberg\Jory\Helpers;

use JosKolenberg\Jory\Exceptions\JoryException;

/**
 * Class KeyRepository.
 */
class KeyRepository
{
    /**
     * @var bool
     */
    protected $minified = true;

    /**
     * @var array
     */
    protected $map = [
        // Filters
        'flt' => 'filter',
        'f' => 'field',
        'o' => 'operator',
        'd' => 'data',
        'and' => 'group_and',
        'or' => 'group_or',
        // Sorts
        'srt' => 'sorts',
        // Fields
        'fld' => 'fields',
        // Relations
        'rlt' => 'relations',
        // Offset & limit
        'ofs' => 'offset',
        'lmt' => 'limit',
    ];

    /**
     * Set the get() method to return minified (or full) keys.
     *
     * @param bool $minified
     * @return \JosKolenberg\Jory\Helpers\KeyRepository
     */
    public function minified(bool $minified): self
    {
        $this->minified = $minified;

        return $this;
    }

    /**
     * Get the full for the given key.
     *
     * @param string $key
     * @return string
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function getFull(string $key): string
    {
        if (array_key_exists($key, $this->map)) {
            return $this->map[$key];
        }
        if (in_array($key, $this->map)) {
            return $key;
        }
        throw new JoryException('Key '.$key.' is no valid Jory key.');
    }

    /**
     * Get the minified key for the given key.
     *
     * @param string $key
     * @return string|null
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function getMinified(string $key): ?string
    {
        if (array_key_exists($key, $this->map)) {
            return $key;
        }

        $key = array_search($key, $this->map);
        if ($key === false) {
            throw new JoryException('Key '.$key.' is no valid Jory key.');
        }

        return $key;
    }

    /**
     * Get a key.
     *
     * @param string $key
     * @param bool|null $minified
     * @return string|null
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function get(string $key, bool $minified = null): ?string
    {
        if (is_null($minified)) {
            $minified = $this->minified;
        }

        return $minified ? $this->getMinified($key) : $this->getFull($key);
    }

    /**
     * Get an array of both the minified and full key.
     *
     * @param string $key
     * @return array
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function getBoth(string $key): array
    {
        return [
            $this->getMinified($key),
            $this->getFull($key),
        ];
    }

    /**
     * Get the value in an array on an given key.
     * Checks both on full and minified key.
     *
     * @param array $array
     * @param string $key
     * @return mixed
     * @throws \JosKolenberg\Jory\Exceptions\JoryException
     */
    public function getArrayValue(array $array, string $key)
    {
        foreach ($this->getBoth($key) as $loopKey) {
            if (array_key_exists($loopKey, $array)) {
                return $array[$loopKey];
            }
        }
    }
}
