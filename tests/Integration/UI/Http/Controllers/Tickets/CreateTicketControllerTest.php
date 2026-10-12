<?php

declare(strict_types=1);

namespace App\Tests\Integration\UI\Http\Controllers\Tickets;

use App\Tests\Lib\ModelFactory\TicketModelFactory;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateTicketControllerTest extends WebTestCase
{
    public function testCreatesTicket(): void
    {
        $ticket = TicketModelFactory::create();
        $data = [
            'subject' => $ticket->subject(),
            'description' => $ticket->description(),
            'customer_email' => $ticket->customer()->email(),
            'customer_name' => $ticket->customer()->name(),
            'category_id' => '8e56a31f-fe37-4dd5-831f-edff5bd6452e',
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            '/tickets',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data),
        );
        $response = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        Assert::assertSame('{}', $response);
    }

    public function testReturnsValidationErrorsWhenMissingPayload(): void
    {
        $client = static::createClient();
        $client->request(
            method: 'POST',
            uri: '/tickets',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode([]),
        );
        $response = json_decode($client->getResponse()->getContent(), true);
        Assert::assertSame([
            'subject' => 'This value is required.',
            'description' => 'This value is required.',
            'customer_email' => 'This value is required.',
            'customer_name' => 'This value is required.',
            'category_id' => 'This value is required.',
        ], $response['errors']);
    }

    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $expectedErrors
     * @return void
     */
    #[DataProvider('invalidRequestPayloadsProvider')]
    public function testReturnsValidationErrorsWhenInvalidRequestBody(array $payload, array $expectedErrors): void
    {
        $client = static::createClient();
        $client->request(
            method: 'POST',
            uri: '/tickets',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($payload),
        );
        $response = json_decode($client->getResponse()->getContent(), true);
        Assert::assertArrayIsEqualToArrayOnlyConsideringListOfKeys(
            $expectedErrors,
            $response['errors'],
            array_keys($expectedErrors)
        );
    }

    public static function invalidRequestPayloadsProvider(): \Generator
    {
        yield 'Invalid email address' => [
            ['customer_email' => 'invalid-email-address'],
            ['customer_email' => 'This value is not a valid email address.']
        ];

        yield 'Invalid category id' => [
            ['category_id' => 'invalid-uuid'],
            ['category_id' => 'This is not a valid UUID.']
        ];
    }
}
