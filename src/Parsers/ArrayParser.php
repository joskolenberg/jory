<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 06-09-18
 * Time: 20:52
 */

namespace JosKolenberg\Jory\Parsers;


use JosKolenberg\Jory\Contracts\JoryParserInterface;
use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Support\AndFilterGroup;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\FilterGroup;
use JosKolenberg\Jory\Support\OrFilterGroup;

class ArrayParser implements JoryParserInterface
{

    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function getJory(): Jory
    {
        $jory = new Jory();
        $this->setFilters($jory);
        return $jory;
    }

    protected function setFilters(Jory $jory)
    {
        $data = $this->data['filter'];

        $jory->setFilter($this->getFilterFromData($data));

//        $c = new FilterCollection();
//        $c->push(new Filter('name', 'like', '%john%'));
//        $jory->setFilters($c);
//        $this->filters = new FilterCollection();
//
//        if(!property_exists($this->data, 'filters')) return;
//
//        foreach ($data->filters as $filter) {
//            $this->filters->push(new Filter($filter->field,
//                $filter->operator,
//                $filter->value,
//                property_exists($filter,'additional') ? $filter->additional : null));
//        }
    }

    private function getFilterFromData($data)
    {
        if(array_key_exists('field', $data)){
            return new Filter($data['field'], $data['operator'], $data['value'], $data['meta']);
        }
        if(array_key_exists('and', $data)){
            $group = new AndFilterGroup();
            foreach ($data['and'] as $filter){
                $group->push($this->getFilterFromData($filter));
            }
            return $group;
        }
        if(array_key_exists('or', $data)){
            $group = new OrFilterGroup();
            foreach ($data['or'] as $filter){
                $group->push($this->getFilterFromData($filter));
            }
            return $group;
        }
    }
}