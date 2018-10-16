<?php

namespace JosKolenberg\Jory\Tests\Parsers;


use JosKolenberg\Jory\Exceptions\JoryException;
use JosKolenberg\Jory\Parsers\ArrayParser;
use PHPUnit\Framework\TestCase;

class ArrayParserSortTest extends TestCase
{

    /** @test */
    function it_can_parse_an_empty_sorts_array_which_results_in_the_sorts_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([
            'sorts' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertEmpty($jory->getSorts());
    }

    /** @test */
    function it_can_parse_an_asc_sort()
    {
        $parser = new ArrayParser([
            'sorts' => [
                'name' => 'asc',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('asc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    function it_can_parse_an_desc_sort()
    {
        $parser = new ArrayParser([
            'sorts' => [
                'name' => 'desc',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('desc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    function it_throws_an_exception_when_an_invalid_sort_is_passed()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sorts order should be asc or desc. (Location: sorts.name)');

        new ArrayParser([
            'sorts' => [
                'name' => 'wrong',
            ],
        ]);
    }

}