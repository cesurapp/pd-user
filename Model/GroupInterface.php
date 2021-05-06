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
    public function getName(): ?string;
    public function setName(string $name): self;
    public function getRoles(): ?array;
    public function setRoles(array $roles): self;
    public function addRole(string $role): self;
    public function removeRole(string $role): self;
    public function hasRole(string $role): bool;
}
