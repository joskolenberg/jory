<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 06-09-18
 * Time: 20:54
 */

namespace JosKolenberg\Jory\Parsers;


use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Jory;

class JsonParser implements JoryParserInterface
{

    /**
     * @var string
     */
    private $json;

    public function __construct(string $json)
    {
        $this->json = $json;
    }

    public function getJory(): Jory
    {
        return (new ArrayParser(json_decode($this->json, true)))->getJory();
    }
}