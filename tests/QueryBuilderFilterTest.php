<?php

namespace JosKolenberg\Jory\Tests;

use JosKolenberg\Jory\Jory;
use JosKolenberg\Jory\Tests\Models\Person;
use JosKolenberg\Jory\Tests\QueryBuilders\PersonBuilder;

/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 04-09-18
 * Time: 21:21
 */
class QueryBuilderFilterTest extends TestCase
{

    /**
     * @test
     */
    public function it_can_apply_a_single_filter()
    {
        $actual = PersonBuilder::array([
            "filter" => [
                "field" => "name",
                "operator" => "like",
                "value" => "%john%",
                "meta" => [],
            ]
        ])->get()->pluck('id')->toArray();

        $this->assertEquals([7, 8, 10], $actual);
    }

    /**
     * @test
     */
    public function it_can_apply_an_OR_filter_group()
    {
        $actual = PersonBuilder::array([
            "filter" => [
                "or" => [
                    [
                        "field" => "name",
                        "operator" => "like",
                        "value" => "%paul%",
                        "meta" => []
                    ],
                    [
                        "field" => "name",
                        "operator" => "like",
                        "value" => "%lennon%",
                        "meta" => []
                    ]
                ]
            ]
        ])->get()->pluck('id')->toArray();

        $this->assertEquals([7, 9, 10], $actual);
    }

    /**
     * @test
     */
    public function it_can_apply_an_AND_filter_group()
    {
        $actual = PersonBuilder::array([
            "filter" => [
                "and" => [
                    [
                        "field" => "name",
                        "operator" => "like",
                        "value" => "%john%",
                        "meta" => []
                    ],
                    [
                        "field" => "name",
                        "operator" => "like",
                        "value" => "%lennon%",
                        "meta" => []
                    ]
                ]
            ]
        ])->get()->pluck('id')->toArray();

        $this->assertEquals([10], $actual);
    }

    /**
     * @test
     */
    public function it_can_apply_a_custom_filter()
    {
        $actual = PersonBuilder::array([
            "filter" => [
                "field" => "inBeatles",
                "operator" => "like",
                "value" => true,
                "meta" => []
            ]
        ])->get()->pluck('id')->toArray();

        $this->assertEquals([9, 10, 11, 12], $actual);

//        $actual = PersonBuilder::array([
//            "filter" => [
//                "field" => "hasBandWithNumberOfBandmembers",
//                "operator" => "<=",
//                "value" => 3,
//                "meta" => []
//            ]
//        ])->get()->pluck('id')->toArray();
//
//        $this->assertEquals([13,14,15], $actual);
    }

    /**
     * @test
     */
//    public function it_can_apply_multiple_custom_filters()
//    {
//        $actual = PersonBuilder::array([
//            "filter" => [
//                "or" => [
//                    [
//                        "field" => "inBeatles",
//                        "operator" => "like",
//                        "value" => true,
//                        "meta" => []
//                    ],
//                    [
//                        "field" => "hasBandWithNumberOfBandmembers",
//                        "operator" => "<=",
//                        "value" => 3,
//                        "meta" => []
//                    ]
//                ]
//            ]
//        ])->get()->pluck('id')->toArray();
//
//        $this->assertEquals([9, 10, 11, 12, 13, 14, 15], $actual);
//    }
//
//    /**
//     * @test
//     */
//    public function it_can_apply_multiple_filters()
//    {
//        $queryBuilder = new PersonBuilder(new Jory('
//            {
//                "filters": [
//                    {
//                        "field": "name",
//                        "operator": "like",
//                        "value": "%mick%"
//                    },
//                    {
//                        "field": "email",
//                        "operator": "=",
//                        "value": "mick.jagger@kolenberg.net"
//                    }
//                ]
//            }'));
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = Person::query()
//            ->where('name', 'like', '%mick%')
//            ->where('email', '=', 'mick.jagger@kolenberg.net')
//            ->toSql();
//
//        $this->assertEquals($actual, $expected);
//    }
//
//    /**
//     * @test
//     */
//    public function it_doesnt_apply_any_filter_when_parameter_is_omitted()
//    {
//        $queryBuilder = new PersonBuilder(new Jory('{}'));
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = Person::query()->toSql();
//
//        $this->assertEquals($actual, $expected);
//    }
//
//    /**
//     * @test
//     */
//    public function it_can_apply_a_custom_filter()
//    {
//        $queryBuilder = new PersonBuilder(new Jory('
//            {
//                "filters": [
//                    {
//                        "field": "custom_field",
//                        "operator": "=",
//                        "value": "keith"
//                    }
//                ]
//            }'));
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = Person::query()->where('modified_field_name', '=', 'keith')->toSql();
//
//        $this->assertEquals($actual, $expected);
//    }
//
//    /**
//     * @test
//     */
//    public function it_can_pass_additional_data_to_a_custom_filter()
//    {
//        $queryBuilder = new PersonBuilder(new Jory('
//            {
//                "filters": [
//                    {
//                        "field": "custom_field_with_additional_data",
//                        "operator": "=",
//                        "value": "keith",
//                        "additional": {
//                            "admin": true
//                        }
//                    }
//                ]
//            }'));
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = Person::query()
//            ->where('modified_field_name', '=', 'keith')
//            ->where('admin', true)
//            ->toSql();
//
//        $this->assertEquals($actual, $expected);
//
//        $queryBuilder = new PersonBuilder(new Jory('
//            {
//                "filters": [
//                    {
//                        "field": "custom_field_with_additional_data",
//                        "operator": "=",
//                        "value": "keith",
//                        "additional": {
//                            "admin": false
//                        }
//                    }
//                ]
//            }'));
//
//        $actual = $queryBuilder->query()->toSql();
//        $expected = Person::query()
//            ->where('modified_field_name', '=', 'keith')
//            ->toSql();
//
//        $this->assertEquals($actual, $expected);
//    }

}