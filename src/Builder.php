<?php
declare(strict_types = 1);

namespace SLWDC\NICParser;


use SLWDC\NICParser\Exception\InvalidArgumentException;

class Builder {

  /**
   * @var \DateTime
   */
  private $birthday;

  /**
   * @var string
   */
  private $gender;

  /**
   * @var int
   */
  private $serial_number;

  public function setParser(Parser $parser) {
    $this->birthday = $parser->getBirthday();
    $this->gender = $parser->getGender();
    $this->serial_number = $parser->getSerialNumber();
  }

  public function setBirthday(\DateTime $date) {
    $this->birthday = clone $date;
    return $this;
  }

  public function setGender(string $gender = 'M') {
    if ($gender === 'M' || $gender === 'F') {
      $this->gender = $gender;
      return $this;
    }
    throw new InvalidArgumentException('Unknown gender. Allowed values are: "M" and "F');
  }

  public function setSerialNumber(int $serial_number) {
    $this->serial_number = $serial_number;
  }

  public function getNumber(): string {
    $this->checkBuilderFields();

    $year = $this->birthday->format('Y');
    $start_date = (new \DateTime())->setDate((int) $year, 1, 1)->setTime(0, 0);
    $birth_date_count = (int) $this->birthday->diff($start_date)->format('%a');

    ++$birth_date_count;

    if ($this->gender === 'F') {
      $birth_date_count += 500;
    }

    $serial = $this->serial_number;
    return "{$year}{$birth_date_count}{$serial}";
  }

  public function getParser(): Parser {
    $number = $this->getNumber();
    return new Parser($number);
  }

  public function checkBuilderFields() {
    if (!$this->birthday) {
      throw new \BadMethodCallException('Attempting to build ID number without a valid birthday set.');
    }
    if (!$this->gender) {
      throw new \BadMethodCallException('Attempting to build ID number without a valid gender set.');
    }
    if (!$this->serial_number) {
      throw new \BadMethodCallException('Attempting to build ID number without a valid serial number set.');
    }
  }
}
