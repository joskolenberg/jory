<?php

namespace JosKolenberg\Jory\Parsers;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Contracts\JoryParserInterface;

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
     *
     * @throws JoryException
     */
    public function __construct(string $json)
    {
        $array = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JoryException('Jory string is no valid json.');
        }
        $this->arrayParser = new ArrayParser($array);
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
