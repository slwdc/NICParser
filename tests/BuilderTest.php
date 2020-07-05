<?php

namespace SLWDC\NICParser\Tests;

use DateTime;
use SLWDC\NICParser\Builder;
use PHPUnit\Framework\TestCase;
use SLWDC\NICParser\Exception\BadMethodCallException;
use SLWDC\NICParser\Exception\InvalidArgumentException;
use SLWDC\NICParser\Parser;

class BuilderTest extends TestCase {

  public function testBuilderFromArbitraryValues_Male(): void {
    $birthday = new DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender();
    $builder->setSerialNumber(25738);

    $this->assertSame('199226025738', $builder->getNumber());

    $parser = new Parser('199226025738');

    $new_builder = new Builder();
    $new_builder->setParser($parser);
    $this->assertSame('199226025738', $builder->getNumber());
  }

  public function testBuilderFromArbitraryValues_GenderNumberAdjustment(): void {
    $birthday = new DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender('F');
    $builder->setSerialNumber(25738);

    $this->assertSame('199276025738', $builder->getNumber());
  }

  public function testInvalidArguments(): void {
    $builder = new Builder();
    $builder->setGender('F');
    $builder->setGender();

    $this->expectException(InvalidArgumentException::class);
    $builder->setGender('T');
  }

  public function testInsufficientData_Birthday(): void {
    $builder = new Builder();
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }

  public function testInsufficientData_Gender(): void {
    $birthday = new DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }

  public function testInsufficientData_SerialNumber(): void {
    $birthday = new DateTime();
    $birthday->setDate(1992, 9, 16);
    $birthday->setTime(0, 0);

    $builder = new Builder();
    $builder->setBirthday($birthday);
    $builder->setGender();
    $this->expectException(BadMethodCallException::class);
    $builder->getNumber();
  }
}
