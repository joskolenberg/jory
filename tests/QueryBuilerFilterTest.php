<?php

namespace JosKolenberg\Jory\Tests;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Tests\Models\TestModel;
use JosKolenberg\Jory\Tests\QueryBuilders\TestModelQueryBuilder;

/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:21
 */
class QueryBuilerFilterTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_apply_a_single_filter()
    {
        $queryBuilder = new TestModelQueryBuilder(new Jory('
            {
                "filters": [
                    {
                        "field": "name",
                        "operator": "like",
                        "value": "%mick%"
                    }
                ]
            }'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()->where('name', 'like', '%mick%')->toSql();

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function it_can_apply_multiple_filters()
    {
        $queryBuilder = new TestModelQueryBuilder(new Jory('
            {
                "filters": [
                    {
                        "field": "name",
                        "operator": "like",
                        "value": "%mick%"
                    },
                    {
                        "field": "email",
                        "operator": "=",
                        "value": "mick.jagger@kolenberg.net"
                    }
                ]
            }'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()
            ->where('name', 'like', '%mick%')
            ->where('email', '=', 'mick.jagger@kolenberg.net')
            ->toSql();

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function it_doesnt_apply_any_filter_when_parameter_is_omitted()
    {
        $queryBuilder = new TestModelQueryBuilder(new Jory('{}'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()->toSql();

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function it_can_apply_a_custom_filter()
    {
        $queryBuilder = new TestModelQueryBuilder(new Jory('
            {
                "filters": [
                    {
                        "field": "custom_field",
                        "operator": "=",
                        "value": "keith"
                    }
                ]
            }'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()->where('modified_field_name', '=', 'keith')->toSql();

        $this->assertEquals($actual, $expected);
    }

    /**
     * @test
     */
    public function it_can_pass_additional_data_to_a_custom_filter()
    {
        $queryBuilder = new TestModelQueryBuilder(new Jory('
            {
                "filters": [
                    {
                        "field": "custom_field_with_additional_data",
                        "operator": "=",
                        "value": "keith",
                        "additional": {
                            "admin": true
                        }
                    }
                ]
            }'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()
            ->where('modified_field_name', '=', 'keith')
            ->where('admin', true)
            ->toSql();

        $this->assertEquals($actual, $expected);

        $queryBuilder = new TestModelQueryBuilder(new Jory('
            {
                "filters": [
                    {
                        "field": "custom_field_with_additional_data",
                        "operator": "=",
                        "value": "keith",
                        "additional": {
                            "admin": false
                        }
                    }
                ]
            }'));

        $actual = $queryBuilder->query()->toSql();
        $expected = TestModel::query()
            ->where('modified_field_name', '=', 'keith')
            ->toSql();

        $this->assertEquals($actual, $expected);
    }

}