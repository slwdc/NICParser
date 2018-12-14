<?php

namespace SLWDC\NICParser\Tests;

use Ayesh\CaseInsensitiveArray\Strict;
use SLWDC\NICParser\Exception\InvalidArgumentException;
use SLWDC\NICParser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase {
  public function getInvalidSamples() {
    $data = [];
    $data['201626085730v'] = ['201626085730v', 102]; // should not be a V at end.
    $data['187526085730'] = ['187526085730', 200]; // year out of accepted range.
    $data['20162608573v'] = ['20162608573v', 102]; // should not be a V at end.
    $data['922608573x'] = ['922608573x', 103]; // x is invalid.
    $data[] = [time(), 103]; // invalid char at end ().
    $data['199336678548'] = ['199336678548', 201]; // day overflow.
    $data['199236778548'] = ['199236778548', 201]; // day overflow.
    $data['foobar'] = ['foobar', 102]; // invalid length.
    $data['abcdepoghtyd'] = ['abcdepoghtyd', 102]; // invalid chars.
    $data[] = ['', 102]; // invalid chars.
    $data['ninechars'] = ['ninechars', 102]; // 9 chars is valid, but not all-int
    $data['929782220v'] = ['929782220v', 201]; // Date overflow, female.
    $data['929782220V'] = ['929782220V', 201]; // Date overflow, female.
    $data['X297R2220V'] = ['X29782220V', 102]; // Date overflow, female.

    return $data;
  }

  public function getValidSamples() {
    $data = new Strict();
    $data['922602573v'] = ['922602573v', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 2573, 'gender' => 'M', 'format' => 1]];
    $data['922602573'] = ['922602573', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 2573, 'gender' => 'M', 'format' => 1]];
    //$data['922602573V'] = ['922602573V', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 2573, 'gender' => 'M', 'format' => 1]];
    $data['201626085734'] = ['201626085734', ['year' => 2016, 'month' => 9, 'date' => 16, 'serial' => 85734, 'gender' => 'M', 'format' => 2]];
    $data['199336578548'] = ['199336578548', ['year' => 1993, 'month' => 12, 'date' => 31, 'serial' => 78548, 'gender' => 'M', 'format' => 2]];
    $data['199236578548'] = ['199236578548', ['year' => 1992, 'month' => 12, 'date' => 30, 'serial' => 78548, 'gender' => 'M', 'format' => 2]];
    $data['199136578548'] = ['199136578548', ['year' => 1991, 'month' => 12, 'date' => 31, 'serial' => 78548, 'gender' => 'M', 'format' => 2]];
    $data['199226025738'] = ['199226025738', ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 25738, 'gender' => 'M', 'format' => 2]];
    $data['199226025738_int'] = [199226025738, ['year' => 1992, 'month' => 9, 'date' => 16, 'serial' => 25738, 'gender' => 'M', 'format' => 2]];

    return $data;
  }

  /**
   * @dataProvider getInvalidSamples
   *
   * @param string $id
   * @param $expected_error_code
   */
  public function testValidityChecker(string $id, $expected_error_code) {
    $this->expectException(InvalidArgumentException::class);
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
  public function testIndividualFields(string $id, array $actual_data = []) {
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
