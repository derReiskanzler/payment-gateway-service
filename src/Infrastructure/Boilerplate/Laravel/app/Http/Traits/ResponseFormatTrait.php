<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Traits;

use Allmyhomes\Infrastructure\Boilerplate\Laravel\app\Http\Response\ApplicationResponse;
use ErrorException;

/**
 * Trait ResponseFormatTrait.
 *
 * @property ApplicationResponse $response
 */
trait ResponseFormatTrait
{
    public function getResponse(): ApplicationResponse
    {
        return new ApplicationResponse();
    }

    /**
     * Magically handle calls to certain properties.
     *
     * @param string $key key
     *
     * @throws ErrorException
     *
     * @return mixed
     * @noinspection MagicMethodsValidityInspection
     */
    public function __get(string $key)
    {
        $callable = [
            'response',
        ];

        $method = 'get'.ucfirst($key);
        if (\in_array($key, $callable, true) && method_exists($this, $method)) {
            return $this->{$method}();
        }

        throw new ErrorException('Undefined property '.static::class.'::'.$key);
    }
}
