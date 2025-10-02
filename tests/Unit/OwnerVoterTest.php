<?php

namespace App\Tests\Security\Voter;

use App\Entity\User;
use App\Security\Voter\OwnerVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class OwnerVoterTest extends TestCase
{
    private OwnerVoter $voter;
    private TokenInterface $token;

    protected function setUp(): void
    {
        $this->voter = new OwnerVoter();
        $this->token = $this->createMock(TokenInterface::class);
    }

    private function createSubject(?User $user = null): object
    {
        return new class($user) {
            private ?User $user;
            public function __construct(?User $user) { $this->user = $user; }
            public function getUser(): ?User { return $this->user; }
        };
    }

    private function vote(?User $user, object $subject, string $attribute = OwnerVoter::EDIT): int
    {
        $this->token->method('getUser')->willReturn($user);
        return $this->voter->vote($this->token, $subject, [$attribute]);
    }

    public function testVoteDeniedForAnonymous(): void
    {
        $result = $this->vote(null, $this->createSubject());
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }

    public function testAdminAlwaysGranted(): void
    {
        $admin = (new User())->setRoles(['ROLE_ADMIN']);
        $result = $this->vote($admin, $this->createSubject());
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testOwnerGranted(): void
    {
        $user = new User();
        $result = $this->vote($user, $this->createSubject($user));
        $this->assertSame(VoterInterface::ACCESS_GRANTED, $result);
    }

    public function testOtherUserDenied(): void
    {
        $owner = new User();
        $other = new User();
        $result = $this->vote($other, $this->createSubject($owner));
        $this->assertSame(VoterInterface::ACCESS_DENIED, $result);
    }
}
