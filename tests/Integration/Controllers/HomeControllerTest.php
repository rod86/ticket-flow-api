<?php

declare(strict_types=1);

namespace App\Tests\Integration\Controllers;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $data = json_decode($client->getResponse()->getContent(), true);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('Content-Type', 'application/json');
        $this->assertSame([
            'name' => 'John Doe',
            'age' => 25,
            'is_active' => true,
        ], $data);
    }
}
