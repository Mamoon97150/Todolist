<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    public function testIndexTrue()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexContains()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $this->assertSelectorTextContains('h1','Bienvenue sur Todo List');

    }
}
