<?php
/**
 * Created by PhpStorm.
 * User: joskolenberg
 * Date: 11-09-18
 * Time: 12:54.
 */

namespace JosKolenberg\Jory\Tests\Parsers;

use PHPUnit\Framework\TestCase;
use JosKolenberg\Jory\Parsers\ArrayValidator;
use JosKolenberg\Jory\Exceptions\JoryException;

class ArrayValidatorTest extends TestCase
{
    /** @test */
    public function it_will_throw_an_exception_when_multiple_keys_are_provided()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: filter)');
        (new ArrayValidator([
            'filter' => [
                'f' => 'John',
                'and' => [],
            ],
        ]))->validate();

        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: filter(and).1)');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                        'or' => [],
                    ],
                ],
            ],
        ]))->validate();

        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: filter(and).2(or).0');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                                'and' => [],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_no_valid_key_is_provided()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter should contain one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: filter)');

        (new ArrayValidator([
            'filter' => [
                'no' => 'valid',
                'key' => 'here',
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_an_o_and_operater_parameter_are_provided()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain both an "o" and "operator" parameter, remove one. (Location: filter(and).2(or).1(and).4)');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'field' => 'sub',
                                    ],
                                    [
                                        'field' => 'sub1',
                                    ],
                                    [
                                        'field' => 'sub2',
                                    ],
                                    [
                                        'field' => 'sub3',
                                    ],
                                    [
                                        'field' => 'sub4',
                                        'o' => '=',
                                        'operator' => 'like',
                                    ],
                                    [
                                        'field' => 'sub5',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_a_v_and_value_parameter_are_provided()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain both an "v" and "value" parameter, remove one. (Location: filter(and).2(or).1(and).0)');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'field' => 'sub',
                                        'v' => 'testing',
                                        'value' => 'testing',
                                    ],
                                    [
                                        'field' => 'sub1',
                                    ],
                                    [
                                        'field' => 'sub2',
                                    ],
                                    [
                                        'field' => 'sub3',
                                    ],
                                    [
                                        'field' => 'sub4',
                                    ],
                                    [
                                        'field' => 'sub5',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_field_parameter_is_no_string()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "f" parameter should have a string value. (Location: filter(and).1)');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => true,
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'field' => 'sub',
                                    ],
                                    [
                                        'field' => 'sub1',
                                    ],
                                    [
                                        'field' => 'sub2',
                                    ],
                                    [
                                        'field' => 'sub3',
                                    ],
                                    [
                                        'field' => 'sub4',
                                    ],
                                    [
                                        'field' => 'sub5',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_operator_parameter_is_provided_but_is_no_string()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "operator" (or "o") parameter should have a string value or be omitted. (Location: filter(and).3)');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'field' => 'sub',
                                    ],
                                    [
                                        'field' => 'sub1',
                                    ],
                                    [
                                        'field' => 'sub2',
                                    ],
                                    [
                                        'field' => 'sub3',
                                    ],
                                    [
                                        'field' => 'sub4',
                                    ],
                                    [
                                        'field' => 'sub5',
                                    ],
                                ],
                            ],
                        ],
                    ],
                    [
                        'f' => 'date_of_birth',
                        'o' => 1123,
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_an_andFilter_is_no_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "and" parameter should hold an array with filters. (Location: filter(and).2(or).1');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => 'wrong',
                            ],
                        ],
                    ],
                    [
                        'f' => 'date_of_birth',
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_an_orFilter_is_no_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "or" parameter should hold an array with filters. (Location: filter(and).2');
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => 'wrong_again',
                    ],
                    [
                        'f' => 'date_of_birth',
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_will_not_throw_an_exception_when_valid_data_is_provided()
    {
        (new ArrayValidator([
            'filter' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'v' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'v' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'v' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'field' => 'sub',
                                    ],
                                    [
                                        'field' => 'sub1',
                                    ],
                                    [
                                        'field' => 'sub2',
                                    ],
                                    [
                                        'field' => 'sub3',
                                    ],
                                    [
                                        'field' => 'sub4',
                                    ],
                                    [
                                        'field' => 'sub5',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_does_not_throw_an_exception_when_no_relations_are_provided()
    {
        (new ArrayValidator([]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_does_not_throw_an_exception_when_an_empty_relations_array_is_provided()
    {
        (new ArrayValidator([
            'relations' => [],
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_an_exception_when_relations_key_is_not_an_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The relation parameter should be an array.');

        (new ArrayValidator([
            'relations' => 'wrong',
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_rlt_key_is_not_an_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The relation parameter should be an array.');

        (new ArrayValidator([
            'rlt' => 'wrong',
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_a_relations_name_is_empty()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A relations name should not be empty. (Location: relations)');

        (new ArrayValidator([
            'rlt' => [
                '' => [],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_jory_data_in_the_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter should contain one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: user.filter)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'filter' => [
                        'wrong' => 'parameter',
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_jory_data_in_the_relation_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter should contain one of the these fields: "f", "field", "and", "group_and", "or" or "group_or". (Location: user.filter(and).1(or).0)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'filter' => [
                        'and' => [
                            [
                                'f' => 'test',
                            ],
                            [
                                'or' => [
                                    [
                                        'wrong' => 'test',
                                    ],
                                    [
                                        'wrong' => 'test',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_if_jor_has_valid_sorts()
    {
        (new ArrayValidator([
            'srt' => [
                'user' => 'asc',
                'car' => 'desc',
            ],
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_if_jor_has_a_valid_sort_2()
    {
        (new ArrayValidator([
            'sorts' => [
                'user' => 'asc',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_if_jor_has_a_valid_sort_3()
    {
        (new ArrayValidator([
            'sorts' => [
                'user' => 'desc',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_a_invalid_order_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sorts order should be string asc or desc. (Location: sorts.user)');
        (new ArrayValidator([
            'sorts' => [
                'user' => 'wrong',
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_a_invalid_order_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sorts order should be string asc or desc. (Location: sorts.user)');
        (new ArrayValidator([
            'sorts' => [
                'user' => [
                    'totally' => 'wrong',
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_a_invalid_field_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The key for a sort item must be a string. (Location: sorts)');
        (new ArrayValidator([
            'sorts' => [
                'test',
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_a_invalid_field_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The key for a sort item must be a string. (Location: sorts)');
        (new ArrayValidator([
            'sorts' => [
                [
                    'totally' => 'wrong',
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_a_sort_in_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sorts order should be string asc or desc. (Location: user.sorts.name)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'srt' => [
                        'name' => 'wrong',
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_offset_value_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The offset parameter should be an integer value. (Location: offset)');
        (new ArrayValidator([
            'ofs' => 'not_a_number',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_offset_value_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The offset parameter should be an integer value. (Location: offset)');
        (new ArrayValidator([
            'offset' => '123',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_offset_value_3()
    {
        (new ArrayValidator([
            'ofs' => 123,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_the_offset_value_4()
    {
        (new ArrayValidator([
            'offset' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_the_limit_value_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The limit parameter should be an integer value. (Location: limit)');
        (new ArrayValidator([
            'lmt' => 'not_a_number',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_limit_value_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The limit parameter should be an integer value. (Location: limit)');
        (new ArrayValidator([
            'limit' => '123',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_limit_value_3()
    {
        (new ArrayValidator([
            'lmt' => 123,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_the_limit_value_4()
    {
        (new ArrayValidator([
            'limit' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_an_omitted_fields_array()
    {
        (new ArrayValidator([
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_nulled_fields_array()
    {
        (new ArrayValidator([
            'fields' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_an_empty_array()
    {
        (new ArrayValidator([
            'fields' => [
                'first_name',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_a_single_field()
    {
        (new ArrayValidator([
            'fields' => [
                'first_name',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_multiple_fields()
    {
        (new ArrayValidator([
            'fields' => [
                'first_name',
                'last_name',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_invalid_content()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The fields parameter must be an array. (Location: fields)');

        (new ArrayValidator([
            'fields' => 'this_is_npt_an_array',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_invalid_keys()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The fields parameter can only contain strings. (Location: fields.1)');

        (new ArrayValidator([
            'fields' => [
                'valid',
                [
                    'not',
                    'valid',
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_a_fields_array_in_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The fields parameter can only contain strings. (Location: users.fields.1)');

        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'fields' => [
                        'valid',
                        [
                            'not',
                            'valid',
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }
}
