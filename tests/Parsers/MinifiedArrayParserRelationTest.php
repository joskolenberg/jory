<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Exceptions\JoryException;

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
        $this->assertEquals('user', $jory->getRelations()[0]->getName());
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
        $this->assertEquals('user', $jory->getRelations()[0]->getName());
        $this->assertEquals('cars', $jory->getRelations()[1]->getName());
    }

    /** @test */
    public function it_can_parse_nested_relations_in_dot_notation()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'user.comments' => [],
                'user.roles.log' => [],
                'user' => [
                    'fld' => ['last_name'],
                ],
                'user.roles.relatedUser' => [
                    'fld' => ['first_name'],
                ],
                'cars' => [],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertCount(2, $jory->getRelations());

        $usersRelation = $jory->getRelations()[0];
        $this->assertEquals('user', $usersRelation->getName());
        $this->assertEquals(['last_name'], $usersRelation->getJory()->getFields());
        $this->assertEquals('comments', $usersRelation->getJory()->getRelations()[0]->getName());
        $rolesRelation = $usersRelation->getJory()->getRelations()[1];
        $this->assertEquals('roles', $rolesRelation->getName());
        $this->assertEquals('log', $rolesRelation->getJory()->getRelations()[0]->getName());
        $relatedUserRelation = $rolesRelation->getJory()->getRelations()[1];
        $this->assertEquals('relatedUser', $relatedUserRelation->getName());
        $this->assertEquals(['first_name'], $relatedUserRelation->getJory()->getFields());

        $carsRelation = $jory->getRelations()[1];
        $this->assertEquals('cars', $carsRelation->getName());
    }

    /** @test */
    public function it_can_give_an_error_message_on_a_dot_notated_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The sorts parameter should be an array. (Location: user.roles.relatedUser.sorts)');

        $parser = new ArrayParser([
            'rlt' => [
                'user.comments' => [],
                'user.roles.log' => [],
                'user' => [
                    'fld' => ['last_name'],
                ],
                'user.roles.relatedUser' => [
                    'srt' => 'wrong',
                ],
                'cars' => [],
            ],
        ]);
        $parser->getJory();
    }
}
