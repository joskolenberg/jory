<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Exceptions\JoryException;

class MinifiedArrayParserSortTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_sorts_array_which_results_in_the_sorts_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([
            'srt' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertEmpty($jory->getSorts());
    }

    /** @test */
    public function it_can_parse_an_asc_sort()
    {
        $parser = new ArrayParser([
            'srt' => [
                'name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('asc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    public function it_can_parse_an_desc_sort()
    {
        $parser = new ArrayParser([
            'srt' => [
                '-name',
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals('name', $jory->getSorts()[0]->getField());
        $this->assertEquals('desc', $jory->getSorts()[0]->getOrder());
    }

    /** @test */
    public function it_throws_an_exception_when_an_invalid_sort_is_passed()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sort item must be a string. (Location: sorts)');

        (new ArrayParser([
            'srt' => [
                [
                    'name' => 'wrong',
                ],
            ],
        ]))->getJory();
    }
}
