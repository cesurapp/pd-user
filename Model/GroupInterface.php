<?php

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
     * @return $this
     */
    public function setName($name);

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param array $roles
     * @return $this
     */
    public function setRoles(array $roles);

    /**
     * @param $role
     * @return $this
     */
    public function addRole($role);

    /**
     * @param $role
     * @return $this
     */
    public function removeRole($role);

    /**
     * @param $role
     * @return bool
     */
    public function hasRole($role);
}
