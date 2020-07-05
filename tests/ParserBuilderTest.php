<?php

namespace SLWDC\NICParser\Tests;

use SLWDC\NICParser\Builder;
use PHPUnit\Framework\TestCase;
use SLWDC\NICParser\Parser;

class ParserBuilderTest extends TestCase {
    public function testParserIdentifcalToBuilder(): void {
        $year = 1992;
        $month = 9;
        $day = 12;
        $serial = '2190';
        $gender = 'M';
        $id = '19922562190';

        $builder = new Builder();
        $builder->setSerialNumber($serial)
            ->setGender($gender)
            ->setBirthday(new \DateTime("{$day}.{$month}.{$year}"));

        $number = $builder->getNumber();
        $this->assertSame($number, $id);

        $parser = new Parser($number);
        $this->assertSame($serial, $parser->getSerialNumber());
        $this->assertSame($gender, $parser->getGender());
    }
}
