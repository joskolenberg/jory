<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use JosKolenberg\Jory\Parsers\ArrayParser;
use PHPUnit\Framework\TestCase;

class ArrayParserRelationTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_relations_array_which_results_in_the_relations_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([
            'relations' => [],
        ]);
        $jory = $parser->getJory();
        $this->assertEmpty($jory->getRelations());
    }

    /** @test */
    public function it_can_parse_no_relations_key_which_results_in_the_relations_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([]);
        $jory = $parser->getJory();
        $this->assertEmpty($jory->getRelations());
    }

    /** @test */
    public function it_can_parse_a_relation()
    {
        $parser = new ArrayParser([
            'relations' => [
                'user' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(1, $jory->getRelations());
        $this->assertEquals('user', $jory->getRelations()[0]->getName());
    }

    /** @test */
    public function it_can_parse_multiple_relations()
    {
        $parser = new ArrayParser([
            'relations' => [
                'user' => [],
                'cars' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(2, $jory->getRelations());
        $this->assertEquals('user', $jory->getRelations()[0]->getName());
        $this->assertEquals('cars', $jory->getRelations()[1]->getName());
    }
}
