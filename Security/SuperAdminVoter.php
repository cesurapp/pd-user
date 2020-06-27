<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Security;

use Pd\UserBundle\Model\User;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Super Admin All Access Voter.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class SuperAdminVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        $excluded = [
            'IS_AUTHENTICATED_ANONYMOUSLY',
            'IS_AUTHENTICATED_FULLY',
            'IS_AUTHENTICATED_REMEMBERED',
            'ISGRANTED_VOTER',
            'ROLE_PREVIOUS_ADMIN',
            'IS_IMPERSONATOR',
        ];

        if (!\is_array($attribute)) {
            $attribute = [$attribute];
        }

        foreach ($attribute as $item) {
            if (\in_array($item, $excluded, false)) {
                return false;
            }
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        // Get User
        $user = $token->getUser();

        // Check Login
        if (!$user instanceof UserInterface) {
            return false;
        }

        // Check All Access
        if (\in_array(User::ROLE_ALL_ACCESS, $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
