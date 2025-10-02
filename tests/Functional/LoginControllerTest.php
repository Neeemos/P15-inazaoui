<?php

namespace App\Tests\Functional;

class LoginControllerTest extends BaseWebCase
{
    public function testGuestCanAccessPanelAndSeeAddButton(): void
    {
        $client = $this->createUserClient();

        $this->assertSelectorExists(
            'li.nav-item a.nav-link[href="/admin/media"]:contains("Panel")'
        );

        $crawler = $client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists(
            'a.btn.btn-primary[href="/admin/media/add"]:contains("Ajouter")'
        );
    }

    public function testAdminCanAccessPanelAndSeeAddButton(): void
    {
        $client = $this->createAdminClient();

        $this->assertSelectorExists(
            'li.nav-item a.nav-link[href="/admin/media"]:contains("Panel")'
        );

        $crawler = $client->request('GET', '/admin/guests/');
        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists(
            'a.btn.btn-primary[href="/admin/guests/add"]:contains("Ajouter un invité")'
        );
    }
    public function testUserIsLocked(): void
    {
        $client = $this->createUserLockedClient();
        $this->assertResponseIsSuccessful();


        $this->assertSelectorExists('div.alert.alert-danger[role="alert"]');

        $this->assertSelectorTextContains(
            'div.alert.alert-danger[role="alert"]',
            'Votre compte est bloqué.'
        );
    }
}
