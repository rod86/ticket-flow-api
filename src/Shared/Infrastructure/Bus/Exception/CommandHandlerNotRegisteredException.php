<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Exception;

final class CommandHandlerNotRegisteredException extends \Exception
{
    public function __construct(string $className)
    {
        $message = \sprintf(
            'Command with class %s has no handler registered',
            $className
        );
        parent::__construct($message);
    }
}
