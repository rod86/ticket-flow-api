<?php

declare(strict_types=1);

namespace App\Tests\Lib\ModelFactory;

use App\Tests\Lib\ValueGenerator\FakeValueGenerator;
use App\Tickets\Domain\TicketCategory;

final class TicketCategoryModelFactory
{
    public static function create(
        ?string $id = null,
        ?string $name = null,
    ): TicketCategory {
        return new TicketCategory(
            id: $id ?? FakeValueGenerator::uuid(),
            name: $name ?? FakeValueGenerator::string(),
        );
    }
}
