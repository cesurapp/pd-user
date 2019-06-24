<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Kerem APAYDIN <kerem@apaydin.me>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Event;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * User Account Events.
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
class UserEvent extends Event
{
    public const REGISTER_BEFORE = 'user.register_before';
    public const REGISTER = 'user.register';
    public const REGISTER_CONFIRM = 'user.register_confirm';
    public const RESETTING = 'user.resetting';
    public const RESETTING_COMPLETE = 'user.resetting_complete';

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
     * @param UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get User.
     *
     * @return UserInterface
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
     *
     * @param Response $response
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
    public function hasResponse(): bool
    {
        return null !== $this->response;
    }
}
