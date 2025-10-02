<?php

namespace App\Tests\Functional;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Entity\User;
use App\Entity\Media;

class MediaControllerTest extends BaseWebCase
{
    protected function tearDown(): void
    {
        parent::tearDown();
        static::ensureKernelShutdown();
    }

    private function createTempImage(string $prefix, string $color = 'white'): UploadedFile
    {
        $filePath = tempnam(sys_get_temp_dir(), $prefix) . '.jpg';
        $image = imagecreatetruecolor(10, 10);

        $col = ($color === 'white')
            ? imagecolorallocate($image, 255, 255, 255)
            : imagecolorallocate($image, 0, 0, 0);

        imagefilledrectangle($image, 0, 0, 10, 10, $col);
        imagejpeg($image, $filePath);
        imagedestroy($image);

        return new UploadedFile(
            $filePath,
            $prefix . '.jpg',
            'image/jpeg',
            null,
            true
        );
    }

    private function getUserByEmail(string $email): User
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        return $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    private function getMediaByTitle(string $title): ?Media
    {
        $entityManager = static::getContainer()->get('doctrine')->getManager();
        return $entityManager->getRepository(Media::class)->findOneBy(['title' => $title]);
    }

    public function testAdminCanAddMedia(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/media/add');
        $this->assertResponseIsSuccessful();

        $uploadedFile = $this->createTempImage('test_media', 'white');
        $adminUser = $this->getUserByEmail('admin@test.local');

        $form = $crawler->selectButton('Ajouter')->form([
            'media[title]' => 'Media de test',
            'media[album]' => 2,
            'media[user]' => $adminUser->getId(),
        ]);
        $form['media[file]']->upload($uploadedFile);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();


        $media = $this->getMediaByTitle('Media de test');
        $this->assertNotNull($media, 'Le média devrait être présent en base.');
    }

    public function testAdminCanUpdateMedia(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();

        $link = $crawler->filter('a.btn.btn-primary:contains("Modifier")')->link();
        $crawler = $client->click($link);

        $uploadedFile = $this->createTempImage('test_media_update', 'black');
        $adminUser = $this->getUserByEmail('admin@test.local');

        $form = $crawler->selectButton('Ajouter')->form([
            'media[title]' => 'Titre modifié',
            'media[user]' => $adminUser->getId(),
        ]);
        $form['media[file]']->upload($uploadedFile);

        $client->submit($form);
        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();


        $media = $this->getMediaByTitle('Titre modifié');
        $this->assertNotNull($media, 'Le média modifié devrait exister en base.');
    }

    public function testAdminCanDeleteMedia(): void
    {
        $client = $this->createAdminClient();
        $crawler = $client->request('GET', '/admin/media');
        $this->assertResponseIsSuccessful();

        $firstMedia = $crawler->filter('img[width="75"]')->first();
        $this->assertGreaterThan(0, $firstMedia->count());

        $deleteLink = $crawler->filter('a.btn.btn-danger:contains("Supprimer")')->first()->link();
        $client->click($deleteLink);

        $this->assertTrue($client->getResponse()->isRedirect());
        $client->followRedirect();


        $deleted = $this->getMediaByTitle('Titre modifié');
        $this->assertNull($deleted, 'Le média supprimé ne doit plus exister en base.');
    }
}
