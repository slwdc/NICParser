<?php

namespace SLWDC\NICParser\Tests;

use SLWDC\NICParser\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase {

  public function testBuilderFromArbitraryValues() {
    $birthday = new \DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender('M');
    $builder->setSerialNumber(25738);

    $this->assertSame('199226025738', $builder->getNumber());

    $parser = $builder->getParser();

    $new_builder = new Builder();
    $new_builder->setParser($parser);
    $this->assertSame('199226025738', $builder->getNumber());
  }
}
