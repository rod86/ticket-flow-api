<?php

declare(strict_types=1);

namespace App\Tickets\Domain\Interfaces;

use App\Tickets\Domain\Customer;

interface CustomerRepositoryInterface
{
    public function findByEmail(string $id): ?Customer;
}
