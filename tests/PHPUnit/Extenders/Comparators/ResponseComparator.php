<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Extenders\Comparators;

use DateTimeImmutable;
use SebastianBergmann\Comparator\ObjectComparator;
use Symfony\Component\HttpFoundation\Response;

class ResponseComparator extends ObjectComparator
{
    public function accepts($expected, $actual): bool
    {
        return $expected instanceof Response && $actual instanceof Response;
    }

    /**
     * @param mixed      $expected
     * @param mixed      $actual
     * @param float      $delta
     * @param false      $canonicalize
     * @param false      $ignoreCase
     * @param array<int> $processed
     */
    public function assertEquals(
        $expected,
        $actual,
        $delta = 0.0,
        $canonicalize = false,
        $ignoreCase = false,
        array &$processed = []
    ): void {
        $this->compare($expected, $actual);
    }

    private function compare(Response $expected, Response $actual): void
    {
        $fixedDate = new DateTimeImmutable();
        $expected->setDate($fixedDate);
        $actual->setDate($fixedDate);
        parent::assertEquals($expected, $actual);
    }
}
