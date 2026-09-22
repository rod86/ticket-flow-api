<?php

declare(strict_types=1);

namespace App\UI\Http\ValueResolver;

use App\UI\Http\Request\AbstractJsonRequest;
use App\UI\Http\Validation\Exception\RequestValidationException;
use App\UI\Http\Validation\RequestValidatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final readonly class JsonRequestValueResolver implements ValueResolverInterface
{
    public function __construct(
        private RequestValidatorInterface $validator,
    ) {
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return iterable<AbstractJsonRequest>
     * @throws RequestValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $type = $argument->getType();
        if ($type === null || !is_subclass_of($type, AbstractJsonRequest::class)) {
            return [];
        }

        $content = $request->getContent();

        if (trim($content) === '') {
            $body = [];
        } else {
            try {
                $body = json_decode($request->getContent(), true, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new BadRequestHttpException('The request body contains invalid JSON.', $e);
            }
        }

        $jsonRequest = new $type($body, $request);

        $errors = $this->validator->validate($body, $jsonRequest->validationRules());
        if (!empty($errors)) {
            throw new RequestValidationException($errors);
        }

        yield $jsonRequest;
    }
}
