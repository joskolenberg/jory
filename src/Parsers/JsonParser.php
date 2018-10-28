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
    private $json;

    /**
     * JsonParser constructor.
     *
     * @param string $json
     */
    public function __construct(string $json)
    {
        $this->json = $json;
    }

    /**
     * Get the Jory object based on the given data.
     *
     * @return Jory
     * @throws JoryException
     */
    public function getJory(): Jory
    {
        $array = json_decode($this->json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new JoryException('Jory string is no valid json.');
        }
        return (new ArrayParser($array))->getJory();
    }
}
