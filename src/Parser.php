<?php
declare(strict_types = 1);

namespace SLWDC\NICParser;

use SLWDC\NICParser\Exception\InvalidArgumentException;

class Parser {
  private $data_components = [];

  const ID_FORMAT_PRE_2016 = 1;
  const ID_FORMAT_2016 = 2;

  public function __construct(string $id_number) {
    $this->parse($id_number);
  }

  public function getBirthday(): \DateTime {
    return $this->data_components['date'];
  }

  public function getSerialNumber(): int {
    return $this->data_components['serial'];
  }

  public function getFormat(): int {
    return $this->data_components['format'];
  }

  public function getGender(): string {
    return $this->data_components['gender'];
  }

  private function parse(string $id_number) {
    $id_number = $this->checkLength($id_number);
    $this->checkBirthDate($id_number);
    $this->detectFormat($id_number);
  }

  private function checkLength(string $id_number): int {
    $id_number = strtoupper($id_number);
    $strlen = strlen($id_number);

    if ($strlen === 10) {
      if ($id_number[9] !== 'V') {
        throw new InvalidArgumentException('Ending character is invalid.', 103);
      }
      $id_number = substr($id_number, 0, 9);
    }

    if (!ctype_digit($id_number)) {
      throw new InvalidArgumentException('Provided number is not all-numeric', 102);
    }
    return (int) $id_number;
  }

  private function checkBirthDate(int $id_number) {
    $full_number = strlen((string) $id_number) === 9
      ? '19' . $id_number
      : (string) $id_number;

    $year = (int) substr($full_number, 0, 4);
    $this->data_components['year'] = $year;

    $this->checkBirthYear($year);
    $this->buildBirthDateObject($full_number, $year);
    $this->data_components['serial'] = (int) substr($full_number, 7);
  }

  private function checkBirthYear(int $year) {
    if ($year < 1900 || $year > 2100) {
      throw new InvalidArgumentException('Birth year is out ff 1900-2100 range', 200);
    }
  }

  private function buildBirthDateObject(string $full_number, int $year) {
    $birthday = new \DateTime();
    $birthday->setDate($year, 1, 1)->setTime(0, 0, 0);
    $birth_days_since = (int) substr($full_number, 4, 3);

    if ($birth_days_since > 500) {
      $birth_days_since -= 500;
      $this->data_components['gender'] = 'F';
    }
    else {
      $this->data_components['gender'] = 'M';
    }

    if (date('L', mktime(0, 0, 0, 1, 1, $year)) == 1) {
      --$birth_days_since;
    } else {
      $birth_days_since -= 2;
    }

    $birthday->add(new \DateInterval('P' . $birth_days_since . 'D'));
    $this->data_components['date'] = $birthday;
    if ($birthday->format('Y') !== (string) $year) {
      throw new InvalidArgumentException('Birthday indicator is invalid.', 201);
    }
  }

  private function detectFormat(int $id_number) {
    $strlen = strlen((string) $id_number);
    if ($strlen === 12) {
      $this->data_components['format'] = static::ID_FORMAT_2016;
    }
    else {
      $this->data_components['format'] = static::ID_FORMAT_PRE_2016;
    }
  }
}
