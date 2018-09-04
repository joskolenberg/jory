<?php

use JosKolenberg\Jory\Filter;
use JosKolenberg\Jory\QueryBuilder;
use JosKolenberg\Jory\Tests\Models\TestModel;

/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:21
 */

class QueryBuilerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @test
     */
    public function it_can_process_filters()
    {
        $q = TestModel::query();
//        $jory = new \JosKolenberg\Jory\Jory();
//        $jory->addFilter(new Filter('name', 'like', '%mick%'));
//
//        $queryBuilder = new QueryBuilder(TestModel::query(), $jory);
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = TestModel::query()->where('name', 'like', ' %mick%');
//
//        $this->assertEquals($actual, $expected);
    }

}