<?php

/**
 * This file is part of the pd-admin pd-menu package.
 *
 * @package     pd-user
 *
 * @license     LICENSE
 * @author      Kerem APAYDIN <kerem@apaydin.me>
 *
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Account Events.
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
class UserEvent extends Event
{
    const REGISTER_BEFORE = 'user.register_before';
    const REGISTER = 'user.register';
    const REGISTER_CONFIRM = 'user.register_comfirm';
    const RESETTING = 'user.resetting';
    const RESETTING_COMPLETE = 'user.resetting_complete';
    
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * MainNavEvent constructor.
     *
     * @param UserInterface $menu
     */
    public function __construct(UserInterface $user)
    {
        $this->$user = $user;
    }

    /**
     * Get Menu.
     *
     * @return ItemInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
