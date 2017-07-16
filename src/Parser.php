<?php
declare(strict_types = 1);

namespace SLWDC\NICParser;

use SLWDC\NICParser\Exception\InvalidIdentityCardNumberException;

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
  }

  private function checkLength(string $id_number): int {
    $id_number = strtoupper($id_number);
    $strlen = strlen($id_number);

    switch ($strlen) {
      case 9:
        if (!ctype_digit($id_number)) {
          throw new InvalidIdentityCardNumberException('Provided string is not all-numeric', 102);
        }
        $this->data_components['format'] = static::ID_FORMAT_PRE_2016;
        return (int) $id_number;

      case 10:
        if ($id_number[9] !== 'V') {
          throw new InvalidIdentityCardNumberException('Ending character is invalid.', 103);
        }
        $id_number = substr($id_number, 0, 9);
        if (!ctype_digit($id_number)) {
          throw new InvalidIdentityCardNumberException('Provided string should be numeric except for the last character.', 102);
        }
        $this->data_components['format'] = static::ID_FORMAT_PRE_2016;
        return (int) $id_number;

      case 12:
        if (!ctype_digit($id_number)) {
          throw new InvalidIdentityCardNumberException('Provided number is not all-numeric', 102);
        }
        $this->data_components['format'] = static::ID_FORMAT_2016;
        return (int) $id_number;

      default:
        throw new InvalidIdentityCardNumberException('Provided number is not of a satisfiable length.', 100);
    }
  }

  private function checkBirthDate(int $id_number) {
    $full_number = strlen((string) $id_number) === 9
      ? '19' . $id_number
      : (string) $id_number;

    $year = (int) substr($full_number, 0, 4);
    $this->data_components['year'] = $year;

    if ($year < 1990 || $year > 2100) {
      throw new InvalidIdentityCardNumberException('Birth year is out ff 1900-2100 range', 200);
    }

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

    --$birth_days_since;

    $birthday->add(new \DateInterval('P' . $birth_days_since . 'D'));
    $this->data_components['date'] = $birthday;
    if ($birthday->format('Y') !== (string) $year) {
      throw new InvalidIdentityCardNumberException('Birthday indicator is invalid.', 201);
    }

    $this->data_components['serial'] = (int) substr($full_number, 7, 4);
  }
}
