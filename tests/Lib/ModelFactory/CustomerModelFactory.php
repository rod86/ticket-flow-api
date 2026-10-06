<?php

declare(strict_types=1);

namespace App\Tests\Lib\ModelFactory;

use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\Customer;

final class CustomerModelFactory
{
    public static function create(
        ?string $id = null,
        ?string $name = null,
        ?string $email = null,
        \DateTimeImmutable|null $createdAt = null,
    ): Customer {
        return new Customer(
            id: $id ?? FakeValueGenerator::uuid(),
            name: $name ?? FakeValueGenerator::name(),
            email: $email ?? FakeValueGenerator::email(),
            createdAt: $createdAt ?? FakeValueGenerator::dateTime('-3 months', 'now'),
        );
    }
}
