<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util;

/**
 * For whatever reason many of our classes have a "toString" and "__toString"-Method.
 * __toString should be present if possible to allow string-casting and automatic implementation of the Stringable-Interface
 * I assume toString is there because it looks nicer to the eye, especially when actually called.
 *
 * To prevent classes to write both methods each time they should use this trait instead.
 * (If you think about dropping one of the methods I would keep the __toString() though)
 */
trait toString
{
    abstract public function toString(): string;

    public function __toString(): string
    {
        return $this->toString();
    }
}
