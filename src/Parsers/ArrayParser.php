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
use JosKolenberg\Jory\Support\FilterGroupAnd;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Support\FilterGroup;
use JosKolenberg\Jory\Support\FilterGroupOr;

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

    }

    private function getFilterFromData($data)
    {
        if(array_key_exists('field', $data)){
            return new Filter($data['field'], $data['operator'], $data['value'], $data['meta']);
        }
        if(array_key_exists('group_and', $data)){
            $group = new FilterGroupAnd();
            foreach ($data['group_and'] as $filter){
                $group->push($this->getFilterFromData($filter));
            }
            return $group;
        }
        if(array_key_exists('group_or', $data)){
            $group = new FilterGroupOr();
            foreach ($data['group_or'] as $filter){
                $group->push($this->getFilterFromData($filter));
            }
            return $group;
        }
    }
}