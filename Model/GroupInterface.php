<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Model;

/**
 * User Group Interface.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface GroupInterface
{
    public function getId(): int;

    /**
     * @return string
     */
    public function getName(): ?string;

    /**
     * @param $name
     *
     * @return $this
     */
    public function setName(string $name): self;

    /**
     * @return array
     */
    public function getRoles(): ?array;

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self;

    /**
     * @param string $role
     *
     * @return $this
     */
    public function addRole(string $role): self;

    /**
     * @param string $role
     *
     * @return $this
     */
    public function removeRole(string $role): self;

    /**
     * @param $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool;
}
