<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class PageControllerTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testAboutpage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/aboutus');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testContactpage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testRentpage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/rent');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }

    public function testBlogpage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/blog');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }
}
