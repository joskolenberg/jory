<?php

namespace JosKolenberg\Jory\Contracts;

use JosKolenberg\Jory\Support\FilterCollection;

interface JoryInterface
{

    public function toJson(): string ;
    public function getFilters(): FilterCollection ;

}