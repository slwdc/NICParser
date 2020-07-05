<?php

declare(strict_types=1);

namespace SLWDC\NICParser;


use DateTime;
use SLWDC\NICParser\Exception\BadMethodCallException;
use SLWDC\NICParser\Exception\InvalidArgumentException;

class Builder {

    private DateTime $birthday;
    private string $gender;
    private string $serial_number;

    public function setParser(Parser $parser): void {
        $this->birthday = $parser->getBirthday();
        $this->gender = $parser->getGender();
        $this->serial_number = $parser->getSerialNumber();
    }

    public function setBirthday(DateTime $date): self {
        $this->birthday = clone $date;

        return $this;
    }

    public function setGender(string $gender = 'M'): self {
        if ($gender === 'M' || $gender === 'F') {
            $this->gender = $gender;

            return $this;
        }
        throw new InvalidArgumentException('Unknown gender. Allowed values are: "M" and "F');
    }

    public function setSerialNumber(string $serial_number): self {
        $this->serial_number = $serial_number;

        return $this;
    }

    public function getNumber(): string {
        $this->checkBuilderFields();

        $year = $this->birthday->format('Y');
        $start_date = (new DateTime())->setDate((int)$year, 1, 1)->setTime(0, 0);
        $birth_date_count = (int)$this->birthday->diff($start_date)->format('%a');

        ++$birth_date_count;

        if ($this->gender === 'F') {
            $birth_date_count += 500;
        }

        $serial = $this->serial_number;

        return "{$year}{$birth_date_count}{$serial}";
    }

    public function checkBuilderFields(): void {
        if (!isset($this->birthday)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid birthday set.');
        }
        if (!isset($this->gender)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid gender set.');
        }
        if (!isset($this->serial_number)) {
            throw new BadMethodCallException('Attempting to build ID number without a valid serial number set.');
        }
    }
}
