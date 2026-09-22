<?php

declare(strict_types=1);

namespace App\Tests\Integration\UI\Http\Controllers\Tickets;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class CreateTicketControllerTest extends WebTestCase
{
    public function testCreatesTicket(): void
    {
        $data = [
            'subject' => 'Test subject',
            'body' => 'Lorem ipsum dolor sit amet.',
            'customer_email' => 'johndoe@email',
            'customer_name' => 'John Doe',
            'category_id' => '2c1a9600-bb4c-4b70-9b92-d3bb5e0c837b',
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
        $this->assertSame('{}', $response);
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

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertSame($response['errors'], [
            'subject' => 'This value is required.',
            'body' => 'This value is required.',
            'customer_email' => 'This value is required.',
            'customer_name' => 'This value is required.',
            'category_id' => 'This value is required.',
        ]);
    }
}
