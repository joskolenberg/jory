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

class JsonParserFilterTest extends TestCase
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
        $this->assertNull($jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_operator()
    {
        $parser = new JsonParser('{"flt":{"f":"name","o":"="}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertNull($jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_a_name_operator_and_data()
    {
        $parser = new JsonParser('{"flt":{"f":"name","o":"=","d":"John"}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertEquals('=', $jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_single_filter_with_only_a_name_and_data()
    {
        $parser = new JsonParser('{"flt":{"f":"name","d":"John"}}');
        $jory = $parser->getJory();
        $this->assertInstanceOf(Filter::class, $jory->getFilter());
        $this->assertEquals('name', $jory->getFilter()->getField());
        $this->assertNull($jory->getFilter()->getOperator());
        $this->assertEquals('John', $jory->getFilter()->getData());
    }

    /** @test */
    public function it_can_parse_a_groupAnd_filter()
    {
        $parser = new JsonParser('{"flt":{"and":[{"f":"first_name","d":"John"},{"f":"last_name","d":"Lennon"}]}}');
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('John', $filter->getByIndex(0)->getData());
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Lennon', $filter->getByIndex(1)->getData());
    }

    /** @test */
    public function it_can_parse_a_groupOr_filter()
    {
        $parser = new JsonParser('{"flt":{"or":[{"f":"first_name","d":"John"},{"f":"last_name","d":"Lennon"}]}}');
        $jory = $parser->getJory();
        $filter = $jory->getFilter();
        $this->assertInstanceOf(GroupOrFilter::class, $filter);
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('John', $filter->getByIndex(0)->getData());
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Lennon', $filter->getByIndex(1)->getData());
    }

    /** @test */
    public function it_can_handle_grouped_filters()
    {
        $parser = new JsonParser('{"flt":{"and":[{"f":"first_name","d":"Eric"},{"f":"last_name","d":"Clapton"},{"or":[{"f":"band","o":"in","d":["beatles","stones"]},{"and":[{"f":"project","o":"like","d":"Cream"},{"f":"drummer","d":"Ginger Baker"}]}]}]}}');

        $jory = $parser->getJory();
        $filter = $jory->getFilter();

        $this->assertInstanceOf(GroupAndFilter::class, $filter);
        $this->assertEquals(3, count($filter));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(0));
        $this->assertEquals('first_name', $filter->getByIndex(0)->getField());
        $this->assertEquals('Eric', $filter->getByIndex(0)->getData());
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(1));
        $this->assertEquals('last_name', $filter->getByIndex(1)->getField());
        $this->assertEquals('Clapton', $filter->getByIndex(1)->getData());
        $this->assertInstanceOf(GroupOrFilter::class, $filter->getByIndex(2));
        $this->assertEquals(2, count($filter->getByIndex(2)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(0));
        $this->assertEquals('band', $filter->getByIndex(2)->getByIndex(0)->getField());
        $this->assertEquals('in', $filter->getByIndex(2)->getByIndex(0)->getOperator());
        $this->assertEquals(['beatles', 'stones'], $filter->getByIndex(2)->getByIndex(0)->getData());
        $this->assertInstanceOf(GroupAndFilter::class, $filter->getByIndex(2)->getByIndex(1));
        $this->assertEquals(2, count($filter->getByIndex(2)->getByIndex(1)));
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(0));
        $this->assertEquals('project', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getField());
        $this->assertEquals('like', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getOperator());
        $this->assertEquals('Cream', $filter->getByIndex(2)->getByIndex(1)->getByIndex(0)->getData());
        $this->assertInstanceOf(Filter::class, $filter->getByIndex(2)->getByIndex(1)->getByIndex(1));
        $this->assertEquals('drummer', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getField());
        $this->assertNull($filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getOperator());
        $this->assertEquals('Ginger Baker', $filter->getByIndex(2)->getByIndex(1)->getByIndex(1)->getData());
    }

    /** @test */
    public function it_throws_an_exception_when_the_validator_fails_3()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "or" (OR group) parameter should be an array with filters. (Location: flt');
        (new JsonParser('{"flt":{"or":"wrong"}}'))->getJory();
    }

    /** @test */
    public function it_throws_an_exception_when_string_is_no_valid_json()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Jory string is no valid json.');
        (new JsonParser('{"flt'))->getJory();
    }

    /** @test */
    public function it_can_parse_nested_relations_in_dot_notation_2()
    {
        $parser = new JsonParser('{"rlt":{"bands.songs":{"fld":["title"]},"bands.albums":{"fld":["name"]}}}');
        $jory1 = $parser->getJory();

        $parser = new JsonParser('{"rlt":{"bands":{"rlt":{	"songs":{"fld":["title"]},"albums":{"fld":["name"]}}}}}');
        $jory2 = $parser->getJory();

        $this->assertEquals($jory1->toJson(), $jory2->toJson());
    }
}
