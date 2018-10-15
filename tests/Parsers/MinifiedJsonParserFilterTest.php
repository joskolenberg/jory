<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 09:16.
 */

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Parsers\JsonParser;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;
use JosKolenberg\Jory\Exceptions\JoryException;

class MinifiedJsonParserFilterTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_filter_which_results_in_the_filter_being_null_in_jory()
    {
        $parser = new JsonParser('{"flt":[]}');
        $jory = $parser->getJory();
        $this->assertNull($jory->getFilter());
    }

    /** @test */
    public function it_can_parse_no_filter_which_results_in_the_filter_being_null_in_jory()
    {
        $parser = new JsonParser('[]');
        $jory = $parser->getJory();
        $this->assertNull($jory->getFilter());
        $parser = new JsonParser('{}');
        $jory = $parser->getJory();
        $this->assertNull($jory->getFilter());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name()
    {
        $parser = new JsonParser('{"flt":{"f":"name"}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_operator()
    {
        $parser = new JsonParser('{"flt":{"f":"name","o":"="}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_a_name_operator_and_value()
    {
        $parser = new JsonParser('{"flt":{"f":"name","o":"=","v":"John"}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_value()
    {
        $parser = new JsonParser('{"flt":{"f":"name","v":"John"}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getValue());
    }

    /** @test */
    public function it_can_parse_a_groupAnd_filter()
    {
        $parser = new JsonParser('{"flt":{"and":[{"f":"first_name","v":"John"},{"f":"last_name","v":"Lennon"}]}}');
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->value);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->value);
    }

    /** @test */
    public function it_can_parse_a_groupOr_filter()
    {
        $parser = new JsonParser('{"flt":{"or":[{"f":"first_name","v":"John"},{"f":"last_name","v":"Lennon"}]}}');
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('John', $filter->getByIndex(0)->value);
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Lennon', $filter->getByIndex(1)->value);
    }

    /** @test */
    public function it_can_handle_grouped_filters()
    {
        $parser = new JsonParser('{"flt":{"and":[{"f":"first_name","v":"Eric"},{"f":"last_name","v":"Clapton"},{"or":[{"f":"band","o":"in","v":["beatles","stones"]},{"and":[{"f":"project","o":"like","v":"Cream"},{"f":"drummer","v":"Ginger Baker"}]}]}]}}');

        $jory = $parser->getJory();
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals(3, count($filter));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(0));
        $this->assertEquals('first_name', $filter->getByIndex(0)->field);
        $this->assertEquals('Eric', $filter->getByIndex(0)->value);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(1));
        $this->assertEquals('last_name', $filter->getByIndex(1)->field);
        $this->assertEquals('Clapton', $filter->getByIndex(1)->value);
        $this->assertInstanceOf(GroupOrFilter::class, $filter->getByIndex(2));
        $this->assertEquals(2, count($filter->getByIndex(2)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(0));
        $this->assertEquals('band', $filter->getByIndex(2)->getByIndex(0)->field);
        $this->assertEquals('in', $filter->getByIndex(2)->getByIndex(0)->operator);
        $this->assertEquals(['beatles', 'stones'], $filter->getByIndex(2)->getByIndex(0)->value);
        $this->assertInstanceOf(GroupAndFilter::class, $filter->getByIndex(2)->getByIndex(1));
        $this->assertEquals(2, count($filter->getByIndex(2)->getByIndex(1)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(0));
        $this->assertEquals('project', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->field);
        $this->assertEquals('like', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->operator);
        $this->assertEquals('Cream', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->value);
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(1));
        $this->assertEquals('drummer', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->field);
        $this->assertNull($filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->operator);
        $this->assertEquals('Ginger Baker', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->value);
    }

    /** @test */
    public function it_throws_an_exception_when_the_validator_fails()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "or" parameter should hold an array with filters. (Location: filter');
        (new JsonParser('{"filter":{"or":"wrong"}}'));
    }
}
