<?php

declare(strict_types=1);

namespace App\UI\Http\ValueResolver;

use App\UI\Http\Request\AbstractJsonRequest;
use App\UI\Validation\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final readonly class JsonRequestValueResolver implements ValueResolverInterface
{
    public function __construct(
        private ValidatorInterface $validator,
    ) {
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<AbstractJsonRequest>
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        /*if (null === $type || !is_subclass_of($type, JsonRequest::class)) {
            return [];
        }*/

        $body = json_decode($request->getContent(), true);
        $jsonRequest = new $type($body);

        $this->validator->validate($body, $jsonRequest->constraints());

        yield $jsonRequest;
    }
}
