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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Switcher.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class SwitchUserVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        return 'CAN_SWITCH_USER' === $attribute && $subject instanceof UserInterface;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface || !$subject instanceof UserInterface) {
            return false;
        }

        // All Access
        if (\in_array(User::ROLE_ALL_ACCESS, $token->getRoleNames(), true)) {
            return true;
        }

        // Check Account Switcher
        if (\in_array('ROLE_ALLOWED_TO_SWITCH', $user->getRoles(), true)) {
            return true;
        }

        return false;
    }
}
