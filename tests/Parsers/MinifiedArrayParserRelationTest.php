<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use JosKolenberg\Jory\Parsers\ArrayParser;
use PHPUnit\Framework\TestCase;

class MinifiedArrayParserRelationTest extends TestCase
{
    /** @test */
    public function it_can_parse_an_empty_relations_array_which_results_in_the_relations_array_being_empty_in_jory()
    {
        $parser = new ArrayParser([
            'rlt' => [],
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
            'rlt' => [
                'user' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(1, $jory->getRelations());
        $this->assertEquals('user', $jory->getRelations()[0]->getRelation());
    }

    /** @test */
    public function it_can_parse_multiple_relations()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'user' => [],
                'cars' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(2, $jory->getRelations());
        $this->assertEquals('user', $jory->getRelations()[0]->getRelation());
        $this->assertEquals('cars', $jory->getRelations()[1]->getRelation());
    }

    /** @test */
    public function it_can_parse_relations_with_an_alias()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'users as active_users' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(1, $jory->getRelations());
        $this->assertEquals('users', $jory->getRelations()[0]->getRelation());
        $this->assertEquals('active_users', $jory->getRelations()[0]->getAlias());
    }

    /** @test */
    public function it_can_parse_a_relation_multiple_times_under_different_aliases()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'users'                 => [],
                'users as active_users' => [
                    'flt' => [
                        'f' => 'active',
                        'o' => '=',
                        'v' => true,
                    ],
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(2, $jory->getRelations());
        $this->assertEquals('users', $jory->getRelations()[0]->getRelation());
        $this->assertNull($jory->getRelations()[0]->getAlias());

        $this->assertEquals('users', $jory->getRelations()[1]->getRelation());
        $this->assertEquals('active_users', $jory->getRelations()[1]->getAlias());
        $this->assertEquals('active', $jory->getRelations()[1]->getJory()->getFilter()->getField());
        $this->assertEquals('=', $jory->getRelations()[1]->getJory()->getFilter()->getOperator());
        $this->assertTrue($jory->getRelations()[1]->getJory()->getFilter()->getValue());
    }
}
