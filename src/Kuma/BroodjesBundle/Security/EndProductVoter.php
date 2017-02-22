<?php
namespace Kuma\BroodjesBundle\Security;

use Kuma\BroodjesBundle\Entity\EndProduct;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EndProductVoter extends Voter
{
    // these strings are just invented: you can use anything
    const DELETE = 'delete';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }


    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::DELETE))) {
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

        if ($this->decisionManager->decide($token, array('ROLE_SUPER_ADMIN'))) {
            return true;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var EndProduct $endProduct */
        $endProduct = $subject;

        switch ($attribute) {
            case self::DELETE:
                return $this->canRemove($endProduct, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canRemove(EndProduct $endProduct, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $endProduct->getUser();
    }
}