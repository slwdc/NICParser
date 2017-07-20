<?php

use SLWDC\NICParser\Builder;

require_once __DIR__ . '/../vendor/autoload.php';

$birthday = new \DateTime();
$birthday->setDate(1992, 9, 16);
$birthday->setTime(0, 0);

$builder = new Builder();
$builder->setBirthday($birthday);
$builder->setGender('M'); // M for male, F for female.
$builder->setSerialNumber(25738);

echo $builder->getNumber(); // "199226025738". This is the new format.
