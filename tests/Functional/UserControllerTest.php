<?php

namespace App\Tests\Functional;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserControllerTest extends BaseWebCase
{
    private ?EntityManagerInterface $em = null;

    protected function tearDown(): void
    {
        if ($this->em) {
            $guests = $this->em->createQueryBuilder()
                ->select('u')
                ->from(User::class, 'u')
                ->where('u.email LIKE :pattern')
                ->setParameter('pattern', 'guest_%@test.local')
                ->getQuery()
                ->getResult();

            foreach ($guests as $guest) {
                $this->em->remove($guest);
            }

            $this->em->flush();
            $this->em->clear();
            $this->em = null;
        }

        parent::tearDown();
    }

    /**
     * Récupère l'EntityManager via le client (lazy, évite de booter le kernel avant createClient()).
     */
    private function getEntityManager($client): EntityManagerInterface
    {
        if (!$this->em) {
            $this->em = $client->getContainer()->get('doctrine')->getManager();
        }

        return $this->em;
    }

    private function getUserByEmail($client, string $email): ?User
    {
        $em = $this->getEntityManager($client);
        return $em->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    public function testShouldReturnGuests(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();

        $guests = $crawler->filter('div.guest.py-5.d-flex.justify-content-between.align-items-center');
        $this->assertGreaterThan(0, $guests->count(), 'Expected at least one guest block.');
    }

    public function testGuestDetailPageHasMedia(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/guests');

        $this->assertResponseIsSuccessful();

        $guest = $crawler->filter('div.guest.py-5.d-flex.justify-content-between.align-items-center')
            ->reduce(function ($node) {
                return preg_match('/\(\d+\)/', $node->filter('h4')->text()) >= 1;
            })
            ->first();

        $this->assertGreaterThan(0, $guest->count(), 'Expected at least one guest with (1) or more items.');

        $link = $guest->selectLink('découvrir')->link();
        $crawler = $client->click($link);

        $this->assertResponseIsSuccessful();

        $mediaBlocks = $crawler->filter('div.col-4.media.mb-4');
        $this->assertGreaterThan(0, $mediaBlocks->count(), 'Expected at least one media block on guest detail page.');
    }

    public function testAboutPageHasTitle(): void
    {
        $client = $this->createUserClient();
        $crawler = $client->request('GET', '/about');
        $this->assertResponseIsSuccessful();

        $title = $crawler->filter('h2.about-title')->text();
        $this->assertSame('Qui suis-je ?', $title, 'Expected to find the about page title "Qui suis-je ?".');
    }

    public function testAdminCanAddGuest(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/guests/add');
        $this->assertResponseIsSuccessful();

        $uniqueEmail = 'guest_' . uniqid() . '@test.local';

        $form = $crawler->selectButton('Ajouter')->form([
            'user[name]'        => 'Invité Test',
            'user[email]'       => $uniqueEmail,
            'user[password]'    => 'password123',
            'user[description]' => 'Utilisateur invité de test',
            'user[roles]'       => ['ROLE_GUEST'],
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect(), 'La soumission devrait rediriger.');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', 'Invité Test', 'Le nouvel invité doit apparaître dans la liste.');


        $guest = $this->getUserByEmail($client, $uniqueEmail);
        $this->assertNotNull($guest, 'L’invité devrait exister en BDD après ajout.');
    }

    public function testAdminCanUpdateGuest(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/guests/');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a.btn.btn-warning:contains("Modifier")')->link();
        $crawler = $client->click($link);
        $this->assertResponseIsSuccessful();

        $newName = 'Invité Modifié ' . uniqid();
        $form = $crawler->selectButton('Ajouter')->form([
            'user[name]'     => $newName,
            'user[password]' => 'password123',
        ]);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect(), 'La soumission du formulaire devrait rediriger.');
        $client->followRedirect();

        $this->assertSelectorTextContains('body', $newName, 'Le nom de l’invité devrait être mis à jour dans la liste.');

        $em = $this->getEntityManager($client);
        $updatedUser = $em->getRepository(User::class)->findOneBy(['name' => $newName]);
        $this->assertNotNull($updatedUser, 'L’invité modifié doit exister en BDD.');
    }

    public function testAdminCanDeleteGuest(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/guests/');
        $this->assertResponseIsSuccessful();

        $row = $crawler->filter('table tr')->reduce(function ($tr) {
            return $tr->filter('a.btn.btn-danger:contains("Supprimer")')->count() > 0;
        })->first();

        $this->assertGreaterThan(0, $row->count(), 'Il doit y avoir au moins un invité avec un bouton Supprimer.');

        $emailCell = $row->filter('td')->eq(1);
        $this->assertGreaterThan(0, $emailCell->count(), 'La ligne doit contenir un email.');
        $email = trim($emailCell->text());

        $link = $row->filter('a.btn.btn-danger:contains("Supprimer")')->link();
        $client->click($link);

        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();

        $this->assertSelectorTextNotContains('table', $email, "L'invité supprimé ($email) ne doit plus apparaître dans la liste.");

        $deletedUser = $this->getUserByEmail($client, $email);
        $this->assertNull($deletedUser, 'L’invité supprimé ne doit plus être présent en BDD.');
    }
}
