<?php

namespace JosKolenberg\Jory\Contracts;


use JosKolenberg\Jory\Jory;

interface JoryParserInterface
{

    /**
     * Get a Jory Object
     *
     * @return Jory
     */
    public function getJory(): Jory;

}