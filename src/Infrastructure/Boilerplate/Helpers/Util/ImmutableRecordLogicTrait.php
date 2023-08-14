<?php

declare(strict_types=1);

namespace Allmyhomes\Infrastructure\Boilerplate\Helpers\Util;

use function array_map;
use EventEngine\Data\ImmutableRecord;
use EventEngine\Data\ImmutableRecordLogic;
use Illuminate\Support\Str;
use InvalidArgumentException;
use function method_exists;
use RuntimeException;
use function sprintf;

/**
 * Overrides some methods to have support for snake_case array keys.
 *
 * @see \EventEngine\Data\ImmutableRecordLogic
 */
trait ImmutableRecordLogicTrait
{
    use ImmutableRecordLogic;

    /**
     * Creates it from snake_case array fields e.g. API input.
     *
     * @param array<string, mixed> $nativeDataSnakeCase Array with snake_case keys
     */
    public static function fromArraySnakeCase(array $nativeDataSnakeCase): static
    {
        return new static(null, null, $nativeDataSnakeCase);
    }

    /**
     * @param array<string,mixed>|null $recordData
     * @param array<string,mixed>|null $nativeData
     * @param array<string,mixed>|null $nativeDataSnakeCase Native data with snake_case array keys
     */
    private function __construct(?array $recordData = null, ?array $nativeData = null, ?array $nativeDataSnakeCase = null)
    {
        if (null === self::$__propTypeMap) {
            self::$__propTypeMap = self::buildPropTypeMap();
        }

        if ($recordData) {
            $this->setRecordData($recordData);
        }

        if ($nativeData) {
            $this->setNativeData($nativeData);
        }

        if ($nativeDataSnakeCase) {
            $this->setNativeDataSnakeCase($nativeDataSnakeCase);
        }

        $this->init();

        $this->assertAllNotNull();
    }

    /**
     * Returns data with snake_case array keys.
     *
     * @return array<string, mixed>
     */
    public function toArraySnakeCase(): array
    {
        $nativeData = [];
        $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

        if (null === self::$__propTypeMap) {
            throw new RuntimeException(sprintf('Got null for $__propTypeMap in %s', static::class));
        }

        foreach (self::$__propTypeMap as $key => [$type, $isNative, $isNullable]) {
            $snakeCaseKey = StringHelper::camelCaseToUnderscore($key);

            switch ($type) {
                case ImmutableRecord::PHP_TYPE_STRING:
                case ImmutableRecord::PHP_TYPE_INT:
                case ImmutableRecord::PHP_TYPE_FLOAT:
                case ImmutableRecord::PHP_TYPE_BOOL:
                case ImmutableRecord::PHP_TYPE_ARRAY:
                    if (
                        \array_key_exists($key, $arrayPropItemTypeMap)
                        && !self::isScalarType($arrayPropItemTypeMap[$key])
                    ) {
                        if ($isNullable && $this->{$key}() === null) {
                            $nativeData[$snakeCaseKey] = null;
                            continue 2;
                        }

                        $nativeData[$snakeCaseKey] = array_map(function ($item) use ($key, &$arrayPropItemTypeMap) {
                            return $this->voTypeToNativeSnakeCase($item, $key, $arrayPropItemTypeMap[$key]);
                        }, $this->{$key}());
                    } else {
                        $nativeData[$snakeCaseKey] = $this->{$key}();
                    }
                    break;
                default:
                    if ($isNullable && $this->{$key}() === null) {
                        $nativeData[$snakeCaseKey] = null;
                        continue 2;
                    }
                    $nativeData[$snakeCaseKey] = $this->voTypeToNativeSnakeCase($this->{$key}(), $key, $type);
            }
        }

        return $nativeData;
    }

    /**
     * @param array<string,mixed> $nativeData Native data with snake_case array keys
     */
    private function setNativeDataSnakeCase(array $nativeData): void
    {
        $recordData = [];
        $arrayPropItemTypeMap = self::getArrayPropItemTypeMapFromMethodOrCache();

        foreach ($nativeData as $key => $val) {
            $camelCaseKey = Str::camel($key);

            if (!isset(self::$__propTypeMap[$camelCaseKey])) {
                throw new InvalidArgumentException(sprintf('Invalid property passed to Record %s. Got property with key %s(%s)', $camelCaseKey, $key, static::class));
            }
            [$type, , $isNullable] = self::$__propTypeMap[$camelCaseKey];

            if (null === $val) {
                if (!$isNullable) {
                    throw new RuntimeException(sprintf('Got null for non nullable property %s of Record %s', $key, static::class));
                }

                $recordData[$camelCaseKey] = null;
                continue;
            }

            $recordData[$camelCaseKey] = match ($type) {
                ImmutableRecord::PHP_TYPE_STRING,
                ImmutableRecord::PHP_TYPE_INT,
                ImmutableRecord::PHP_TYPE_FLOAT,
                ImmutableRecord::PHP_TYPE_BOOL, => $val,

                ImmutableRecord::PHP_TYPE_ARRAY => (\array_key_exists($camelCaseKey, $arrayPropItemTypeMap) && !self::isScalarType($arrayPropItemTypeMap[$camelCaseKey]))
                    ? $this->fromTypeSnakeCaseArray($camelCaseKey, $arrayPropItemTypeMap, $val)
                    : $val,

                default => $this->fromTypeSnakeCase($val, $type)
            };
        }

        $this->setRecordData($recordData);
    }

    /**
     * @param array<string,array<mixed>> $arrayPropItemTypeMap
     *
     * @return array<mixed>
     */
    private function fromTypeSnakeCaseArray(string $camelCaseKey, array &$arrayPropItemTypeMap, mixed $val): array
    {
        return array_map(function ($item) use ($camelCaseKey, &$arrayPropItemTypeMap) {
            /** @var string $type */
            $type = $arrayPropItemTypeMap[$camelCaseKey];

            return $this->fromTypeSnakeCase($item, $type);
        }, $val);
    }

    private function fromTypeSnakeCase(mixed $value, string $type): mixed
    {
        if (\is_array($value)) {
            /* @noinspection PhpUndefinedMethodInspection */
            return $type::fromArraySnakeCase($value);
        }

        return $this->fromType($value, $type);
    }

    private function voTypeToNativeSnakeCase(mixed $value, string $key, string $type): mixed
    {
        if (\is_object($value) && method_exists($value, 'toArraySnakeCase')) {
            return $value->toArraySnakeCase();
        }

        return $this->voTypeToNative($value, $key, $type);
    }
}
