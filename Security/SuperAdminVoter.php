<?php

/**
 * This file is part of the pdAdmin package.
 *
 * @package     pdAdmin
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 pdAdmin
 * @license     LICENSE
 *
 * @link        http://pdadmin.ramazanapaydin.com
 */

namespace Pd\UserBundle\Security;

use Pd\UserBundle\Model\User;
use Pd\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

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
        ];

        if (\in_array($attribute, $excluded, true)) {
            return false;
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
