<?php
namespace App\Voter;

use App\Storage\DataStorage;
use App\Contract\PlatformCentricInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class PlatformConcentricVoter extends Voter {

    public function __construct(private DataStorage $storage)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === "CREATE" && $subject instanceof PlatformCentricInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, \Symfony\Component\Security\Core\Authorization\Voter\Vote|null $vote = null): bool
    {
        return null != $this->storage->getPlatformId();
    }
}
