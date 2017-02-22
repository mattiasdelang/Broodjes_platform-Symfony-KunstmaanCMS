<?php

namespace Kuma\BroodjesBundle\Security;

use Kuma\BroodjesBundle\Entity\EndProduct;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class FavoriteVoter extends Voter
{
    // these strings are just invented: you can use anything
    const FAV = 'fav';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }

    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, [self::FAV])) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof EndProduct) {
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
        /** @var EndProduct $endProduct */
        $endProduct = $subject;

        switch ($attribute) {
            case self::FAV:
                return $this->isOwner($endProduct, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isOwner(EndProduct $endProduct, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $endProduct->getUser();
    }
}
