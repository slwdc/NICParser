# Sri Lankan National Identity Card Number Parser
A PHP library to parse, validate, and generate valid Sri Lankan national identity card numbers.

[![Latest Stable Version](https://poser.pugx.org/slwdc/nic-parser/v/stable)](https://packagist.org/packages/slwdc/nic-parser) [![License](https://poser.pugx.org/slwdc/nic-parser/license)](https://packagist.org/packages/slwdc/nic-parser) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/slwdc/NICParser/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/slwdc/NICParser/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/slwdc/NICParser/badges/build.png?b=master)](https://scrutinizer-ci.com/g/slwdc/NICParser/build-status/master)  [![codecov](https://codecov.io/gh/slwdc/NICParser/branch/master/graph/badge.svg)](https://codecov.io/gh/slwdc/NICParser) [![SensioLabsInsight](https://insight.sensiolabs.com/projects/2e61c5e1-095b-43c5-9fa2-c77527480774/mini.png)](https://insight.sensiolabs.com/projects/2e61c5e1-095b-43c5-9fa2-c77527480774) ![CI](https://github.com/slwdc/NICParser/workflows/CI/badge.svg) [![Too many badges](https://img.shields.io/badge/style-too_many-brightgreen.svg?style=toomany&label=badges)](https://github.com/slwdc/NICParser)
### Installation

You can install the library easily with composer. Requires PHP 7.4 or later.

``` composer require slwdc/nic-parser```

Alternaely, you can download the library from github, and manually include the class or integrate into your own autoloader. See the included `composer.json` file for `PSR-4` namespace mappings.

### Usage

See the [Wikipedia article](https://en.wikipedia.org/wiki/National_identity_card_%28Sri_Lanka%29) for the formats used.

#### Parsing an ID number

    <?php
    
    use SLWDC\NICParser\Parser;
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    /**
     * Example 1
     */
    $id_number = '862348594v';
    
    $parser = new Parser($id_number);
    $parser->getBirthday();// Returns a \DateTime object with the date parsed.
    echo $parser->getBirthday()->format('Y-m-d'); // prints "1986-08-22"
    echo $parser->getGender(); // Prints "M". M for male, F for female.
    echo $parser->getSerialNumber(); // Prints "8594"
    
    /**
     * Example 2
     */
    $id_number = '19935158154';
    
    $parser = new Parser($id_number);
    $parser->getBirthday();// Returns a \DateTime object with the date parsed.
    echo $parser->getBirthday()->format('Y-m-d'); // prints "1993-01-15"
    echo $parser->getGender(); // Prints "F". M for male, F for female.
    echo $parser->getSerialNumber(); // Prints "8154"

#### Validating an ID number
The `Parser` class throws an exception when you instantiate it with an invalid ID number. Make sure you always catch exceptions on validation.

    <?php
    use SLWDC\NICParser\Parser;
    use SLWDC\NICParser\Exception\InvalidArgumentException;
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    /* This is an invalid ID number because 499 here is not indicating a valid
    birth date */
    $id_number = '924998593v';
    
    try {
      $parser = new Parser($id_number);
    }
    catch (\SLWDC\NICParser\Exception\InvalidArgumentException $exception) {
      echo $exception->getMessage(); // "Birthday indicator is invalid."
    }

Depending on the validation error, you will get different messages explaining the situation. All exceptions will be instances of `SLWDC\NICParser\Exception\InvalidArgumentException`.
I'm no good at writing sample / filler text, so go write something yourself.

#### Building an NIC number

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

### Contribute
All contributions are welcome. If you have any questions, please post an issue in the Github issue queue. For any PRs, we'd appreciate if you can add proper test coverage as well. 

### Alternative Implementations

 - [Ksengine/NICParser](https://github.com/Ksengine/NICParser/) - A Python implementation

kthxbye.
