<?php

declare(strict_types=1);

use SebastianBergmann\Comparator\Factory;
use Tests\PHPUnit\Extenders\Comparators\ResponseComparator;

$comparatorFactory = Factory::getInstance();
$comparatorFactory->register(new ResponseComparator());
