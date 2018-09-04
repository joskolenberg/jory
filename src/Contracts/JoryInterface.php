<?php

namespace JosKolenberg\Jory\Contracts;

use JosKolenberg\Jory\FilterCollection;

interface JoryInterface
{

    public function getFilters(): FilterCollection;
//    public function getSorts();
//    public function getFields();
//    public function getRelations();
//
}