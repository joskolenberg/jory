<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Jory;

/**
 * Class to parse a json string with jory data to an Jory object.
 *
 * Class ArrayParser
 */
class JsonParser implements JoryParserInterface
{
    /**
     * @var string
     */
    private $arrayParser;

    /**
     * JsonParser constructor.
     *
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->arrayParser = new ArrayParser(json_decode($json, true));
    }

    /**
     * Get the Jory object based on the given data.
     *
     * @return Jory
     */
    public function getJory(): Jory
    {
        return $this->arrayParser->getJory();
    }
}
