<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:53
 */

namespace JosKolenberg\Jory;


use JosKolenberg\Jory\Contracts\JoryInterface;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\FilterCollection;

class Jory implements JoryInterface
{

    protected $json;
    protected $filters;

    public function __construct(string $json)
    {
        $this->json = $json;
        $data = json_decode($json);
        $this->setFilters($data);
    }

    public function getFilters(): FilterCollection
    {
        return $this->filters;
    }

    public function toJson(): string
    {
        return json_encode($this->data);
    }

    protected function setFilters($data)
    {
        $this->filters = new FilterCollection();

        if(!property_exists($data, 'filters')) return;

        foreach ($data->filters as $filter) {
            $this->filters->push(new Filter($filter->field,
                $filter->operator,
                $filter->value,
                property_exists($filter,'additional') ? $filter->additional : null));
        }
    }

}