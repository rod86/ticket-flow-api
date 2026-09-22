<?php

declare(strict_types=1);

namespace App\UI\Http\ValueResolver;

use App\UI\Http\Request\AbstractJsonRequest;
use App\UI\Validation\ValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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

        try {
            $body = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new BadRequestHttpException('The request body contains invalid JSON.', $e);
        }

        /** @var AbstractJsonRequest $jsonRequest */
        $jsonRequest = new $type($body, $request);

        $this->validator->validate($body, $jsonRequest->validationRules());

        yield $jsonRequest;
    }
}
