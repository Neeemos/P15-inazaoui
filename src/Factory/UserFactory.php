<?php

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->passwordHasher = $passwordHasher;
    }

    public static function class(): string
    {
        return User::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'email' => self::faker()->email(),
            'name' => self::faker()->name(),
            'description' => self::faker()->text(),
            'password' => "", // sera hashé dans initialize()
            'roles' => self::faker()->randomElements(
                ['ROLE_ADMIN', 'ROLE_GUEST', 'IS_LOCKED'],
                self::faker()->numberBetween(1, 3)
            ),
        ];
    }

    protected function initialize(): static
    {
        return $this->afterInstantiate(function (User $user): void {
            if (empty($user->getPassword())) {
                $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            }
        });
    }

    /**
     * Crée un admin statique et le persiste via ObjectManager
     */
    // UserFactory.php

    public static function addAdminStatic(ObjectManager $manager, UserPasswordHasherInterface $passwordHasher): User
    {
        $user = new User();
        $user->setEmail('admin@test.local');
        $user->setName('Admin Test');
        $user->setRoles(['ROLE_ADMIN']);
        $user->setDescription('Administrateur Test du site');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $manager->persist($user);

        return $user;
    }

    public static function addUserStatic(ObjectManager $manager, UserPasswordHasherInterface $passwordHasher): User
    {
        $user = new User();
        $user->setEmail('user@test.local');
        $user->setName('User Test');
        $user->setRoles(['ROLE_GUEST']);
        $user->setDescription('User Test du site');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $manager->persist($user);

        return $user;
    }
    public static function addUserLockedStatic(ObjectManager $manager, UserPasswordHasherInterface $passwordHasher): User
    {
        $user = new User();
        $user->setEmail('userlocked@test.local');
        $user->setName('UserLocked Test');
        $user->setRoles(['IS_LOCKED']);
        $user->setDescription('UserLocked Test du site');
        $user->setPassword($passwordHasher->hashPassword($user, 'password'));

        $manager->persist($user);

        return $user;
    }
}
