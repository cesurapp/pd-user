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
use Symfony\Component\HttpFoundation\Response;
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
    const REGISTER_CONFIRM = 'user.register_confirm';
    const RESETTING = 'user.resetting';
    const RESETTING_COMPLETE = 'user.resetting_complete';

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var Response
     */
    private $response;

    /**
     * MainNavEvent constructor.
     *
     * @param UserInterface $menu
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
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

    /**
     * Returns the response object.
     *
     * @return Response|null
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * Sets a response and stops event propagation.
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        $this->stopPropagation();
    }

    /**
     * Returns whether a response was set.
     *
     * @return bool Whether a response was set
     */
    public function hasResponse()
    {
        return null !== $this->response;
    }
}
