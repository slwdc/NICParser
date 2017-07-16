<?php

namespace SLWDC\NICParser\Tests;

use SLWDC\NICParser\Exception\InvalidIdentityCardNumberException;
use SLWDC\NICParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
  public function getInvalidSamples() {
    return [
      ['201626085730v', 100], // should not be a V at end.
      ['20162608573v', 102], // should not be a V at end.
      ['922608573x', null], // x is invalid.
      [time(), 103], // invalid char at end ().
      ['199336678548', 201], // day overflow.
      ['199236778548', 201], // day overflow.
      ['foobar', 100], // invalid length.
      ['abcdepoghtyd', 102], // invalid chars.
      ['', 100], // invalid chars.
    ];
  }

  public function getValidSamples() {
    return [
      ['922602573v', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 2573, 'gender' => 'M', 'format' => 1]],
      ['201626085734', ['year' => 2016, 'month' => 9, 'date' => 16, 'serial' => 8573, 'gender' => 'M', 'format' => 2]],
      ['199336578548', ['year' => 1993, 'month' => 12, 'date' => 31, 'serial' => 7854, 'gender' => 'M', 'format' => 2]],
      ['199236578548', ['year' => 1992, 'month' => 12, 'date' => 30, 'serial' => 7854, 'gender' => 'M', 'format' => 2]],
      ['199136578548', ['year' => 1991, 'month' => 12, 'date' => 31, 'serial' => 7854, 'gender' => 'M', 'format' => 2]],
    ];
  }

  /**
   * @dataProvider getInvalidSamples
   *
   * @param string $id
   * @param $expected_error_code
   */
  public function testValidityChecker(string $id, $expected_error_code) {
    $this->expectException(InvalidIdentityCardNumberException::class);
    if ($expected_error_code) {
      $this->expectExceptionCode($expected_error_code);
    }
    $parser = new Parser($id);
  }

  /**
   * @dataProvider getValidSamples
   *
   * @param string $id
   * @param array $actual_data
   */
  public function testBirthYear(string $id, array $actual_data = []) {
    $parser = new Parser($id);

    $date = $parser->getBirthday();
    $this->assertSame($actual_data['year'], (int) $date->format('Y'));
    $this->assertSame($actual_data['month'], (int) $date->format('n'));
    $this->assertSame($actual_data['date'], (int) $date->format('j'));

    $this->assertSame($actual_data['gender'], $parser->getGender());

    $this->assertSame($actual_data['serial'], $parser->getSerialNumber());

    $this->assertSame($actual_data['format'], $parser->getFormat());
  }
}
