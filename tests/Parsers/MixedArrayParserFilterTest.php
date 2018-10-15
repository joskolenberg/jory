<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 10:56.
 */

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Support\Filter;
use JosKolenberg\Jory\Parsers\ArrayParser;
use JosKolenberg\Jory\Support\GroupOrFilter;
use JosKolenberg\Jory\Support\GroupAndFilter;

class MixedArrayParserFilterTest extends TestCase
{
    /** @test */
    public function it_can_handle_mixed_imput_of_normal_and_minified_keys()
    {
        $parser = new ArrayParser([
            'filter' => [
                'and' => [
                    [
                        'f'     => 'first_name',
                        'value' => 'Eric',
                    ],
                    [
                        'field' => 'last_name',
                        'v'     => 'Clapton',
                    ],
                    [
                        'or' => [
                            [
                                'f'     => 'band',
                                'o'     => 'in',
                                'value' => ['beatles', 'stones'],
                            ],
                            [
                                'group_and' => [
                                    [
                                        'f'        => 'project',
                                        'operator' => 'like',
                                        'v'        => 'Cream',
                                    ],
                                    [
                                        'f' => 'drummer',
                                        'v' => 'Ginger Baker',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

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
}
