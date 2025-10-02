<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class BaseWebCase extends WebTestCase
{
    protected function createAdminClient(): KernelBrowser
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'admin@test.local',
            '_password' => 'password',
        ]);
        $client->submit($form);
        $client->followRedirect();

        return $client;
    }

    protected function createUserClient(): KernelBrowser
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'user@test.local',
            '_password' => 'password',
        ]);
        $client->submit($form);
        $client->followRedirect();

        return $client;
    }
    protected function createUserLockedClient(): KernelBrowser
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Connexion')->form([
            '_username' => 'userlocked@test.local',
            '_password' => 'password',
        ]);
        $client->submit($form);
        $client->followRedirect();

        return $client;
    }
}
