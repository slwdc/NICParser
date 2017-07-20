<?php

use SLWDC\NICParser\Exception\InvalidArgumentException;
use SLWDC\NICParser\Parser;

require_once __DIR__ . '/../vendor/autoload.php';

/* This is an invalid ID number because 499 here is not indicating a valid
birth date */
$id_number = '924998593v';

try {
  $parser = new Parser($id_number);
}
catch (InvalidArgumentException $exception) {
  echo $exception->getMessage(); // "Birthday indicator is invalid."
}
