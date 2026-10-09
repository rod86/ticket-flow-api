<?php

declare(strict_types=1);

namespace App\Tests\Lib\Fixtures;

use App\Shared\Domain\Entity;
use Doctrine\DBAL\Connection;

/**
 * @template TModel of Entity
 */
abstract class AbstractFixture
{
    /** @var array<string> */
    protected array $ids = [];

    public function __construct(
        protected readonly Connection $connection
    ) {
    }

    /**
     * @param array<string, mixed> $data Model values
     *
     * @return TModel
     */
    abstract public function insert(array $data = []): Entity;

    abstract public function cleanup(): void;

    /** Track an externally-created id (e.g. a row the app inserted) for cleanup. */
    public function register(string $id): void
    {
        $this->ids[] = $id;
    }
}
