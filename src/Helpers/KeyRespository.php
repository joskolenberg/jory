<?php

namespace JosKolenberg\Jory\Helpers;

use JosKolenberg\Jory\Exceptions\JoryException;

class KeyRespository
{

    protected $minified = true;

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

    public function minified(bool $minified): self
    {
        $this->minified = $minified;

        return $this;
    }

    public function getFull(string $key): string
    {
        if(array_key_exists($key, $this->map)){
            return $this->map[$key];
        }
        if(in_array($key, $this->map)){
            return $key;
        }
        throw new JoryException('Key ' . $key . ' is no valid Jory key.');
    }

    public function getMinified(string $key): ?string
    {
        if(array_key_exists($key, $this->map)){
            return $key;
        }
        if(in_array($key, $this->map)){
            return array_search($key, $this->map);
        }
        throw new JoryException('Key ' . $key . ' is no valid Jory key.');
    }

    public function get(string $key, bool $minified = null): ?string
    {
        if(is_null($minified)){
            $minified = $this->minified;
        }
        return $minified ? $this->getMinified($key) : $this->getFull($key);
    }

    public function getBoth(string $key): array
    {
        return [
            $this->getMinified($key),
            $this->getFull($key),
        ];
    }

    public function exists($key): bool
    {
        if(array_key_exists($key, $this->map)){
            return true;
        }
        if(in_array($key, $this->map)){
            return true;
        }
        return false;
    }

    public function getArrayValue(array $array, string $key): string
    {
        foreach ($this->getBoth($key) as $loopKey) {
            if (array_key_exists($loopKey, $array)) {
                return $array[$loopKey];
            }
        }
    }
}