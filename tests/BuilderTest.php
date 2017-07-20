<?php

namespace SLWDC\NICParser\Tests;

use SLWDC\NICParser\Builder;
use PHPUnit\Framework\TestCase;
use SLWDC\NICParser\Exception\BadMethodCallException;
use SLWDC\NICParser\Exception\InvalidArgumentException;

class BuilderTest extends TestCase {

  public function testBuilderFromArbitraryValues_Male() {
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

  public function testBuilderFromArbitraryValues_GenderNumberAdjustment() {
    $birthday = new \DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender('F');
    $builder->setSerialNumber(25738);

    $this->assertSame('199276025738', $builder->getNumber());
  }

  public function testInvalidArguments() {
    $builder = new Builder();
    $builder->setGender('F');
    $builder->setGender('M');

    $this->expectException(InvalidArgumentException::class);
    $builder->setGender('T');
  }

  public function testInsufficientData_Birthday() {
    $builder = new Builder();
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }

  public function testInsufficientData_Gender() {
    $birthday = new \DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }

  public function testInsufficientData_SerialNumber() {
    $birthday = new \DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender('M');
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }
}
