<?php

namespace JosKolenberg\Jory\Converters;

use JosKolenberg\Jory\Jory;

/**
 * Class to convert a Jory object to a Json string.
 *
 * Class ToArrayConverter
 */
class ToJsonConverter
{
    /**
     * @var Jory
     */
    private $jory;
    /**
     * @var bool
     */
    private $minified;

    /**
     * ToJsonConverter constructor.
     *
     * @param Jory $jory
     * @param bool $minified
     */
    public function __construct(Jory $jory, bool $minified = true)
    {
        $this->jory = $jory;
        $this->minified = $minified;
    }

    /**
     * Get the Json string based on given Jory object.
     *
     * @return array
     */
    public function get(): string
    {
        return json_encode((new ToArrayConverter($this->jory, $this->minified))->get());
    }
}
