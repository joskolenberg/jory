<?php

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Exceptions\JoryException;

class ArrayParserOffsetLimitTest extends TestCase
{
    /** @test */
    public function it_can_set_an_offset_value()
    {
        $parser = new ArrayParser([
            'ofs' => 12,
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(12, $jory->getOffset());
    }

    /** @test */
    public function it_can_set_an_offset_value_to_zero()
    {
        $parser = new ArrayParser([
            'ofs' => 0,
        ]);
        $jory = $parser->getJory();
        $this->assertSame(0, $jory->getOffset());
    }

    /** @test */
    public function it_can_set_an_offset_value_in_a_relation()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'users' => [
                    'ofs' => 123,
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(123, $jory->getRelations()[0]->getJory()->getOffset());
    }

    /** @test */
    public function it_can_set_a_null_offset_value()
    {
        $parser = new ArrayParser([
            'ofs' => null,
        ]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getOffset());
    }

    /** @test */
    public function it_set_a_default_null_value_for_offset_when_no_offset_is_given()
    {
        $parser = new ArrayParser([]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getOffset());
    }

    /** @test */
    public function it_throws_an_exception_when_no_valid_offset_value_is_given()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "ofs" (offset) parameter should be an integer value. (Location: ofs)');

        (new ArrayParser([
            'ofs' => 'string',
        ]))->getJory();
    }

    /** @test */
    public function it_throws_an_exception_when_no_valid_offset_value_is_given_in_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "ofs" (offset) parameter should be an integer value. (Location: users.ofs)');

        (new ArrayParser([
            'rlt' => [
                'users' => [
                    'ofs' => 'not_a_number',
                ],
            ],
        ]))->getJory();
    }

    /** @test */
    public function it_can_set_an_limit_value()
    {
        $parser = new ArrayParser([
            'lmt' => 12,
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(12, $jory->getLimit());
    }

    /** @test */
    public function it_can_set_an_limit_value_to_zero()
    {
        $parser = new ArrayParser([
            'lmt' => 0,
        ]);
        $jory = $parser->getJory();
        $this->assertSame(0, $jory->getLimit());
    }

    /** @test */
    public function it_can_set_an_limit_value_in_a_relation()
    {
        $parser = new ArrayParser([
            'rlt' => [
                'users' => [
                    'lmt' => 123,
                ],
            ],
        ]);
        $jory = $parser->getJory();
        $this->assertEquals(123, $jory->getRelations()[0]->getJory()->getLimit());
    }

    /** @test */
    public function it_can_set_a_null_limit_value()
    {
        $parser = new ArrayParser([
            'lmt' => null,
        ]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getLimit());
    }

    /** @test */
    public function it_set_a_default_null_value_for_limit_when_no_limit_is_given()
    {
        $parser = new ArrayParser([]);
        $jory = $parser->getJory();
        $this->assertNull($jory->getLimit());
    }

    /** @test */
    public function it_throws_an_exception_when_no_valid_limit_value_is_given()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "lmt" (limit) parameter should be an integer value. (Location: lmt)');

        (new ArrayParser([
            'lmt' => 'string',
        ]))->getJory();
    }

    /** @test */
    public function it_throws_an_exception_when_no_valid_limit_value_is_given_in_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "lmt" (limit) parameter should be an integer value. (Location: users.lmt)');

        (new ArrayParser([
            'rlt' => [
                'users' => [
                    'lmt' => 'not_a_number',
                ],
            ],
        ]))->getJory();
    }
}
