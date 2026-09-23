<?php

declare(strict_types=1);

namespace App\UI\Http\Request\Tickets;

use App\UI\Http\Request\AbstractJsonRequest;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateTicketRequest extends AbstractJsonRequest
{
    public function validationRules(): array
    {
        return [
            'title' => new Assert\NotBlank(),
            'description' => new Assert\NotBlank(),
            'customer_email' => new Assert\NotBlank(),
            'customer_name' => new Assert\NotBlank(),
            'category_id' => new Assert\NotBlank(),
        ];
    }
}
