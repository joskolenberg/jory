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
        $this->expectExceptionMessage('A filter cannot contain more than one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: flt)');
        (new ArrayValidator([
            'flt' => [
                'f' => 'John',
                'and' => [],
            ],
        ]))->validate();

        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: flt(and).1)');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                        'or' => [],
                    ],
                ],
            ],
        ]))->validate();

        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A filter cannot contain more than one of the fields "f" (field), "and" (AND group) or "or" (OR group). (Location: filter(and).2(or).0');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'd' => 'Beatles',
                                'and' => [],
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
        $this->expectExceptionMessage('The "f" (field) parameter should be a string value. (Location: flt(and).1.f)');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => true,
                        'd' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'd' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'f' => 'sub',
                                    ],
                                    [
                                        'f' => 'sub1',
                                    ],
                                    [
                                        'f' => 'sub2',
                                    ],
                                    [
                                        'f' => 'sub3',
                                    ],
                                    [
                                        'f' => 'sub4',
                                    ],
                                    [
                                        'f' => 'sub5',
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
        $this->expectExceptionMessage('The "o" (operator) parameter should be a string value or should be omitted. (Location: flt(and).3.o)');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'd' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'f' => 'sub',
                                    ],
                                    [
                                        'f' => 'sub1',
                                    ],
                                    [
                                        'f' => 'sub2',
                                    ],
                                    [
                                        'f' => 'sub3',
                                    ],
                                    [
                                        'f' => 'sub4',
                                    ],
                                    [
                                        'f' => 'sub5',
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
        $this->expectExceptionMessage('The "and" (AND group) parameter should be an array with filters. (Location: flt(and).2(or).1');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'd' => 'Beatles',
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
        $this->expectExceptionMessage('The "or" (OR group) parameter should be an array with filters. (Location: flt(and).2');
        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
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
            'flt' => [
                'and' => [
                    [
                        'f' => 'first_name',
                        'd' => 'John',
                    ],
                    [
                        'f' => 'last_name',
                        'd' => 'Lennon',
                    ],
                    [
                        'or' => [
                            [
                                'f' => 'band',
                                'd' => 'Beatles',
                            ],
                            [
                                'and' => [
                                    [
                                        'f' => 'sub',
                                    ],
                                    [
                                        'f' => 'sub1',
                                    ],
                                    [
                                        'f' => 'sub2',
                                    ],
                                    [
                                        'f' => 'sub3',
                                    ],
                                    [
                                        'f' => 'sub4',
                                    ],
                                    [
                                        'f' => 'sub5',
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
            'rlt' => [],
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_an_exception_when_relations_key_is_not_an_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "rlt" (relations) parameter should be an array.');

        (new ArrayValidator([
            'rlt' => 'wrong',
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_rlt_key_is_not_an_array()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "rlt" (relations) parameter should be an array.');

        (new ArrayValidator([
            'rlt' => 'wrong',
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_a_relations_name_is_empty()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A relation\'s name should not be empty. (Location: rlt)');

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
        $this->expectExceptionMessage('Unknown key "wrong" in Jory Query. (Location: user.flt)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'flt' => [
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
        $this->expectExceptionMessage('Unknown key "wrong" in Jory Query. (Location: user.flt(and).1(or).0)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'flt' => [
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
            'srt' => [
                'user' => 'asc',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_if_jor_has_a_valid_sort_3()
    {
        (new ArrayValidator([
            'srt' => [
                'user' => 'desc',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_an_invalid_field_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sort item must be a string. (Location: srt.user)');
        (new ArrayValidator([
            'srt' => [
                'user' => [
                    'totally' => 'wrong',
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_if_a_sort_has_an_invalid_field_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('A sort item must be a string. (Location: srt.1)');
        (new ArrayValidator([
            'srt' => [
                'valid',
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
        $this->expectExceptionMessage('A sort item must be a string. (Location: user.srt.0)');

        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'srt' => [
                        [
                            'name' => 'wrong',
                        ],
                    ],
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_offset_value_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "ofs" (offset) parameter should be an integer value. (Location: ofs)');
        (new ArrayValidator([
            'ofs' => 'not_a_number',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_offset_value_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "ofs" (offset) parameter should be an integer value. (Location: ofs)');
        (new ArrayValidator([
            'ofs' => '123',
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
            'ofs' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_the_limit_value_1()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "lmt" (limit) parameter should be an integer value. (Location: lmt)');
        (new ArrayValidator([
            'lmt' => 'not_a_number',
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_the_limit_value_2()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "lmt" (limit) parameter should be an integer value. (Location: lmt)');
        (new ArrayValidator([
            'lmt' => '123',
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
            'lmt' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_an_omitted_fields_array()
    {
        (new ArrayValidator([]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_nulled_fields_array()
    {
        (new ArrayValidator([
            'fld' => null,
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_an_empty_array()
    {
        (new ArrayValidator([
            'fld' => [],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_a_single_field()
    {
        (new ArrayValidator([
            'fld' => [
                'first_name',
            ],
        ]))->validate();
        $this->assertTrue(true);
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_multiple_fields()
    {
        (new ArrayValidator([
            'fld' => [
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
        $this->expectExceptionMessage('The "fld" (fields) parameter must be an array or string. (Location: fld)');

        (new ArrayValidator([
            'fld' => 123132,
        ]))->validate();
    }

    /** @test */
    public function it_can_validate_a_fields_array_with_invalid_keys()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('The "fld" (fields) parameter can only contain strings. (Location: fld.1)');

        (new ArrayValidator([
            'fld' => [
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
        $this->expectExceptionMessage('The "fld" (fields) parameter can only contain strings. (Location: users.fld.1)');

        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'fld' => [
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

    /** @test */
    public function it_allows_the_field_to_be_a_string_when_a_single_field_is_requested()
    {
        (new ArrayValidator([
            'fld' => 'first_name',
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_allows_the_field_to_be_a_string_when_a_single_field_is_requested_in_a_relation()
    {
        (new ArrayValidator([
            'rlt' => [
                'user' => [
                    'fld' => 'first_name'
                ]
            ]
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_allows_the_sorts_to_be_a_string_when_there_will_be_sorted_on_a_single_field()
    {
        (new ArrayValidator([
            'srt' => 'first_name',
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_allows_the_sorts_to_be_a_string_when_there_will_be_sorted_on_a_single_field_in_a_relation()
    {
        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'srt' => 'first_name'
                ]
            ]
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_allows_the_filter_to_be_a_string_when_there_will_be_filter_on_a_single_boolean_filter()
    {
        (new ArrayValidator([
            'flt' => 'first_name',
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_allows_the_filter_to_be_a_string_when_there_will_be_filter_on_a_single_boolean_filter_in_a_relation()
    {
        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'flt' => 'first_name'
                ]
            ]
        ]))->validate();

        $this->assertTrue(true);
    }

    /** @test */
    public function it_throws_an_exception_when_unknown_keys_are_defined_in_the_root()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "rlts" in Jory Query. (Location: root)');

        (new ArrayValidator([
            'rlts' => [
                'users' => [],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_unknown_keys_are_defined_in_the_root_of_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "flds" in Jory Query. (Location: users.root)');

        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'flds' => 'first_name'
                ],
            ],
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_unknown_keys_are_defined_in_a_filter()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "v" in Jory Query. (Location: flt)');

        (new ArrayValidator([
            'flt' => [
                'v' => 'name',
                'o' => 'like',
                'd' => '%james%',
            ],
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_unknown_keys_are_defined_in_a_nested_filter()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "v" in Jory Query. (Location: flt(and).0)');

        (new ArrayValidator([
            'flt' => [
                'and' => [
                    [
                        'v' => 'name',
                        'o' => 'like',
                        'd' => '%james%',
                    ]
                ]
            ],
        ]))->validate();
    }

    /** @test */
    public function it_throws_an_exception_when_unknown_keys_are_defined_in_a_nested_filter_in_a_relation()
    {
        $this->expectException(JoryException::class);
        $this->expectExceptionMessage('Unknown key "v" in Jory Query. (Location: users.flt(and).0)');

        (new ArrayValidator([
            'rlt' => [
                'users' => [
                    'flt' => [
                        'and' => [
                            [
                                'v' => 'name',
                                'o' => 'like',
                                'd' => '%james%',
                            ]
                        ]
                    ],
                ],
            ],
        ]))->validate();
    }
}
