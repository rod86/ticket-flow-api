<?php

declare(strict_types=1);

namespace App\Tests\Lib\ValueGenerator;

use DateTimeImmutable;
use Faker\Factory;
use Faker\Generator;

final class FakeValueGenerator
{
    private const MAX_INT = 2147483647;

    private static ?Generator $faker = null;

    private static function generator(): Generator
    {
        return self::$faker = self::$faker ?? Factory::create();
    }

    public static function uuid(): string
    {
        return self::generator()->uuid;
    }

    public static function name(): string
    {
        return self::generator()->unique()->name();
    }

    public static function email(): string
    {
        return self::generator()->unique()->safeEmail();
    }

    public static function dateTime(
        \DateTime|string|null $startDate = null,
        \DateTime|string|null $endDate = null
    ): DateTimeImmutable {
        return DateTimeImmutable::createFromMutable(self::generator()->dateTimeBetween(
            $startDate,
            $endDate
        ));
    }

    public static function string(): string
    {
        return self::generator()->word;
    }

    public static function sentence(): string
    {
        return self::generator()->sentence;
    }

    public static function text(): string
    {
        return self::generator()->text;
    }

    public static function plainPassword(): string
    {
        return self::generator()->password(8, 12);
    }

    public static function password(?string $plainPassword = null): string
    {
        return password_hash($plainPassword ?? self::plainPassword(), PASSWORD_DEFAULT);
    }

    public static function token(): string
    {
        return self::generator()->sha256();
    }

    public static function integer(int $min = 0, int $max = self::MAX_INT): int
    {
        return self::generator()->numberBetween($min, $max);
    }

    public static function float(int $min = 0, ?int $max = null, int $decimals = 2): float
    {
        return self::generator()->randomFloat($decimals, $min, $max);
    }

    /**
     * @template T
     *
     * @param array<T> $options
     *
     * @return T
     */
    public static function randomElement(array $options): mixed
    {
        return self::generator()->randomElement($options);
    }

    public static function boolean(): bool
    {
        return self::generator()->boolean();
    }
}
