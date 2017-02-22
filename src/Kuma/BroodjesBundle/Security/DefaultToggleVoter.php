<?php

namespace Kuma\BroodjesBundle\Security;

use Kuma\BroodjesBundle\Entity\UserInfo;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DefaultToggleVoter extends Voter
{
    // these strings are just invented: you can use anything
    const TOGGLE = 'toggle';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::TOGGLE])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof UserInfo) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        if ($this->decisionManager->decide($token, ['ROLE_SUPER_ADMIN'])) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var UserInfo $userInfo */
        $userInfo = $subject;

        switch ($attribute) {
            case self::TOGGLE:
                return $this->isOwner($userInfo, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isOwner(UserInfo $userInfo, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $userInfo->getUser();
    }
}
