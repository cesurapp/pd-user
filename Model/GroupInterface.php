<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 *
 * @license     LICENSE
 * @author      Kerem APAYDIN <kerem@apaydin.me>
 *
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Model;

/**
 * User Group Interface
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
interface GroupInterface
{
    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getName();

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles);

    /**
     * @param $role
     *
     * @return $this
     */
    public function addRole($role);

    /**
     * @param $role
     *
     * @return $this
     */
    public function removeRole($role);

    /**
     * @param $role
     *
     * @return bool
     */
    public function hasRole($role);
}
