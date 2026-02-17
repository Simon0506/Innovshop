<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProductControllerTest extends WebTestCase
{
    public function testPageProductsIsAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/products/all');
        $this->assertResponseIsSuccessful();
    }
}
