<?php

declare(strict_types=1);

namespace App\Tests\Integration\UI\Http\Controllers\Users;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class CreateUserControllerTest extends WebTestCase
{
    public function testCreatesUser(): void
    {
        $data = [
            'email' => 'johndoe@email',
            'name' => 'John Doe',
            'password' => '12345678',
        ];

        $client = static::createClient();
        $client->request(
            'POST',
            '/users',
            server: ['CONTENT_TYPE' => 'application/json'],
            content: json_encode($data),
        );
        $response = $client->getResponse()->getContent();

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
        $this->assertSame('{}', $response);
    }
}
