<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 05-09-18
 * Time: 22:23
 */

namespace JosKolenberg\Jory\Tests\QueryBuilders;

use JosKolenberg\Jory\QueryBuilder;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Tests\Models\TestModel;

class TestModelQueryBuilder extends QueryBuilder
{

    protected function getBaseQuery()
    {
        return TestModel::query();
    }

    protected function applyCustomFieldFilter($query, Filter $filter)
    {
        $query->where('modified_field_name', $filter->getOperator(), $filter->getValue());
    }

    protected function applyCustomFieldWithAdditionalDataFilter($query, Filter $filter)
    {
        $query->where('modified_field_name', $filter->getOperator(), $filter->getValue());

        if($filter->getAdditional()->admin){
            $query->where('admin', true);
        }
    }
}