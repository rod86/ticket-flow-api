<?php

declare(strict_types=1);

namespace App\Tests\Integration\UI\Http\Controllers\Tickets;

use App\Tests\Lib\Fixtures\CustomerFixture;
use App\Tests\Lib\Fixtures\TicketFixture;
use App\Tickets\Domain\TicketCategory;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Assert;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

final class FindTicketByIdControllerTest extends WebTestCase
{
    private TicketFixture $ticketFixture;
    private CustomerFixture $customerFixture;

    protected function tearDown(): void
    {
        $this->ticketFixture->cleanup();
        $this->customerFixture->cleanup();

        parent::tearDown();
    }

    public function testReturnsTicket(): void
    {
        $client = static::createClient();
        $connection = static::getContainer()->get(EntityManagerInterface::class)->getConnection();
        $this->ticketFixture = new TicketFixture($connection);
        $this->customerFixture = new CustomerFixture($connection);

        $customer = $this->customerFixture->insert();
        $category = new TicketCategory(
            id: '8e56a31f-fe37-4dd5-831f-edff5bd6452e',
            name: 'Billing & Payments',
        );
        $ticket = $this->ticketFixture->insert(['customer' => $customer, 'category' => $category]);

        $client->request('GET', '/tickets/' . $ticket->id());

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        Assert::assertSame(
            [
                'id' => $ticket->id(),
                'subject' => $ticket->subject(),
                'description' => $ticket->description(),
                'status' => $ticket->status()->value,
                'customer' => [
                    'id' => $customer->id(),
                    'name' => $customer->name(),
                    'email' => $customer->email(),
                ],
                'category' => [
                    'id' => $category->id(),
                    'title' => $category->name(),
                ],
                'created_at' => $ticket->createdAt()->format(DATE_ATOM),
                'updated_at' => $ticket->updatedAt()->format(DATE_ATOM),
            ],
            json_decode($client->getResponse()->getContent(), true),
        );
    }
}
