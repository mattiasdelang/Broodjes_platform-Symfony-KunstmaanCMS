<?php
namespace Kuma\BroodjesBundle\Security;

use Kuma\BroodjesBundle\Entity\LunchOrder;
use Kunstmaan\AdminBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class LunchOrderVoter extends Voter
{
    // these strings are just invented: you can use anything
    const EDIT = 'edit';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }


    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::EDIT))) {
            return false;
        }

        // only vote on Post objects inside this voter
        if (!$subject instanceof LunchOrder) {
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
        /** @var LunchOrder $endProduct */
        $lunchOrder = $subject;

        switch ($attribute) {
            case self::EDIT:
                return $this->isOwner($lunchOrder, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function isOwner(LunchOrder $order, User $user)
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $order->getUser();
    }
}