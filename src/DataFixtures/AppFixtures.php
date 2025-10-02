<?php

namespace App\DataFixtures;

use App\Entity\Media;
use App\Factory\AlbumFactory;
use App\Factory\MediaFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Factory\UserFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher) {}
    public function load(ObjectManager $manager): void
    {
        UserFactory::createMany(10);
        AlbumFactory::createMany(20);
        MediaFactory::createMany(20);
        MediaFactory::createMediaAlbum1();
        UserFactory::addUserStatic($manager, $this->passwordHasher);
        UserFactory::addUserLockedStatic($manager, $this->passwordHasher);
        UserFactory::addAdminStatic($manager, $this->passwordHasher);

        $manager->flush();
    }
}
