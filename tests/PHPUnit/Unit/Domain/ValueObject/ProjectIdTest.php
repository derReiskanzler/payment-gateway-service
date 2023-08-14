<?php

declare(strict_types=1);

namespace Tests\PHPUnit\Unit\Domain\ValueObject;

use Allmyhomes\Domain\ValueObject\ProjectId;
use PHPUnit\Framework\TestCase;

final class ProjectIdTest extends TestCase
{
    public function testFromInt(): void
    {
        $projectId = ProjectId::fromInt(42);
        $this->assertInstanceOf(
            ProjectId::class,
            $projectId,
            'created project id from string is not instance of expected class: ProjectId.',
        );
    }

    public function testToInt(): void
    {
        $int = 42;
        $projectId = ProjectId::fromInt($int);
        $this->assertEquals(
            $int,
            $projectId->toInt(),
            'created project id from int does not match expected int.',
        );
    }

    public function testToString(): void
    {
        $int = 42;
        $projectId = ProjectId::fromInt($int);
        $this->assertEquals(
            (string) $int,
            (string) $projectId,
            'created project id from int to string cast does not match expected string.',
        );
    }
}
