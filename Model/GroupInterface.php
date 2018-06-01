<?php

/**
 * This file is part of the pdAdmin pdUser package.
 *
 * @package     pdUser
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-user
 */

namespace Pd\UserBundle\Model;

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
