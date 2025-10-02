<?php

namespace App\Tests\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->entityManager = static::getContainer()->get('doctrine')->getManager();
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    public function testUpgradePasswordSuccessfully(): void
    {
        $user = new User();
        $user->setEmail('test_' . uniqid() . '@example.com'); // email unique
        $user->setPassword('old-password');

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $newHashedPassword = 'new-hash';

        $this->userRepository->upgradePassword($user, $newHashedPassword);

        $this->entityManager->refresh($user);
        $this->assertSame($newHashedPassword, $user->getPassword());
    }

    public function testUpgradePasswordThrowsForUnsupportedUser(): void
    {
        $this->expectException(UnsupportedUserException::class);

        $mockUser = $this->createMock(PasswordAuthenticatedUserInterface::class);
        $this->userRepository->upgradePassword($mockUser, 'irrelevant');
    }
}

