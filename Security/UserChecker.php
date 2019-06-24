<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Kerem APAYDIN <kerem@apaydin.me>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Security;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Checker.
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return;
        }

        // Frozen Account
        if ($user->isFreeze()) {
            throw new CustomUserMessageAuthenticationException('The account has been suspended');
        }

        // Activate Account
        if (!$user->isEnabled()) {
            throw new CustomUserMessageAuthenticationException('Account has not been activated');
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        if (!$user instanceof UserInterface) {
            return;
        }
    }
}
