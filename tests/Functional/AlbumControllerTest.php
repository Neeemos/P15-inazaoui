<?php

namespace App\Tests\Functional;

use App\Entity\Album;
use Doctrine\ORM\EntityManagerInterface;

class AlbumControllerTest extends BaseWebCase
{
    private ?EntityManagerInterface $em = null;

    protected function tearDown(): void
    {
        if ($this->em) {
            $albums = $this->em->createQueryBuilder()
                ->select('a')
                ->from(Album::class, 'a')
                ->where('a.name LIKE :pattern OR a.name = :fixed')
                ->setParameter('pattern', 'AlbumTest_%')
                ->setParameter('fixed', 'Mon album de test')
                ->getQuery()
                ->getResult();

            foreach ($albums as $album) {
                $this->em->remove($album);
            }

            $this->em->flush();
            $this->em->clear();
            $this->em = null;
        }

        parent::tearDown();
    }

    private function getEntityManager($client): EntityManagerInterface
    {
        if (!$this->em) {
            $this->em = $client->getContainer()->get('doctrine')->getManager();
        }
        return $this->em;
    }

    private function createAlbum($client, string $name): Album
    {
        $crawler = $client->request('GET', '/admin/album/add');
        $form = $crawler->selectButton('Ajouter')->form([
            'album[name]' => $name,
        ]);
        $client->submit($form);
        $client->followRedirect();

        $em = $this->getEntityManager($client);
        $album = $em->getRepository(Album::class)->findOneBy(['name' => $name]);
        $this->assertNotNull($album, "Album '$name' non trouvé en BDD après ajout.");
        return $album;
    }

    private function findAlbum(string $name): ?Album
    {
        return $this->em->getRepository(Album::class)->findOneBy(['name' => $name]);
    }
    

    private function deleteAlbumFromPage($client, string $name): void
    {
        $crawler = $client->getCrawler();
        $deleteLink = $crawler->filter('tr:contains("' . $name . '")')->selectLink('Supprimer')->link();
        $client->click($deleteLink);
        $this->assertResponseRedirects('/admin/album');
        $client->followRedirect();
    }




    

    public function testCanAccessPublicAlbums(): void
    {
        $client = static::createClient();
        $client->request('GET', '/portfolio');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('div.col-2 a.btn.w-100.py-3[href="/portfolio/1"]');
    }

    public function testCanSeeImageInPortfolioItem(): void
    {
        $client = static::createClient();
        $client->request('GET', '/portfolio/1');

        $this->assertResponseIsSuccessful();

        $this->assertSelectorExists(
            'div.col-4.media.mb-4 img.w-100',
            'Aucune image trouvée dans le portfolio item (album 1).'
        );
    }


    public function testUserCannotAccessAlbumAdmin(): void
    {
        $client = $this->createUserClient();
        $client->request('GET', '/admin/album');
        $this->assertResponseStatusCodeSame(403);
    }



    public function testAdminCanAccessAlbumsAndSeeAddButton(): void
    {
        $client = $this->createAdminClient();
        $client->request('GET', '/admin/album');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('a.btn.btn-primary[href="/admin/album/add"]:contains("Ajouter")');
    }

    public function testAdminCanAddAlbum(): void
    {
        $client = $this->createAdminClient();
        $album = $this->createAlbum($client, 'Mon album de test');

        $this->assertStringContainsString('Mon album de test', $client->getResponse()->getContent());
        $this->assertNotNull($album);
    }

    public function testAdminCanDeleteAlbum(): void
    {
        $client = $this->createAdminClient();
        $uniqueName = 'AlbumTest_' . uniqid();

        $this->createAlbum($client, $uniqueName);
        $this->deleteAlbumFromPage($client, $uniqueName);

        $this->assertStringNotContainsString($uniqueName, $client->getResponse()->getContent());

        $albumDeleted = $this->findAlbum($uniqueName);
        $this->assertNull($albumDeleted, 'Album encore présent en BDD après suppression.');
    }

    public function testAdminCanEditAlbum(): void
    {
        $client = $this->createAdminClient();
        $originalName = 'AlbumTest_' . uniqid();

        $album = $this->createAlbum($client, $originalName);
        $albumId = $album->getId();


        $crawler = $client->request('GET', '/admin/album');
        $editLink = $crawler->filter('a[href="/admin/album/update/' . $albumId . '"]')->link();
        $crawler = $client->click($editLink);

        $newName = $originalName . '_Edited';
        $form = $crawler->selectButton('Modifier')->form([
            'album[name]' => $newName,
        ]);
        $client->submit($form);
        $client->followRedirect();

        $this->assertStringContainsString($newName, $client->getResponse()->getContent());

        $updatedAlbum = $this->findAlbum($newName);
        $this->assertNotNull($updatedAlbum, 'Album modifié non trouvé en BDD.');
    }
}
