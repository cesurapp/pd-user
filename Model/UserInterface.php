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

use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * User Interface.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface UserInterface extends BaseUserInterface
{
    /**
     * @return int
     */
    public function getId(): int;

    /**
     * @return ProfileInterface
     */
    public function getProfile(): ?ProfileInterface;

    /**
     * @param ProfileInterface $profile
     *
     * @return $this
     */
    public function setProfile(ProfileInterface $profile): self;

    /**
     * @param $username
     *
     * @return $this
     */
    public function setUsername(string $username): self;

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword(string $password): self;

    /**
     * @return string
     */
    public function getEmail(): ?string;

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail(string $email): self;

    public function isEnabled(): bool;

    /**
     * @param $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): self;

    /**
     * @return bool
     */
    public function isFreeze(): bool;

    /**
     * @param $enabled
     *
     * @return $this
     */
    public function setFreeze(bool $enabled): self;

    /**
     * @return \DateTime
     */
    public function getLastLogin(): ?\DateTime;

    /**
     * @param \DateTime|null $time
     *
     * @return $this
     */
    public function setLastLogin(\DateTime $time = null): self;

    /**
     * @return string
     */
    public function getConfirmationToken(): ?string;

    /**
     * @param string $confirmationToken
     *
     * @return $this
     */
    public function setConfirmationToken(?string $confirmationToken): self;

    /**
     * @return $this
     */
    public function createConfirmationToken(): self;

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $date
     *
     * @return $this
     */
    public function setPasswordRequestedAt(\DateTime $date = null): self;

    /**
     * @param $ttl
     */
    public function isPasswordRequestNonExpired($ttl): bool;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $time
     *
     * @return $this
     */
    public function setCreatedAt(\DateTime $time = null): self;

    /**
     * @return array
     */
    public function getRolesUser(): ?array;

    /**
     * @param array $roles
     *
     * @return $this
     */
    public function setRoles(array $roles): self;

    /**
     * @param $role
     *
     * @return $this
     */
    public function addRole(string $role): self;

    /**
     * @param $role
     *
     * @return $this
     */
    public function removeRole(string $role): self;

    /**
     * @param string $role
     *
     * @return bool
     */
    public function hasRole(string $role): bool;

    /**
     * @return array|null
     */
    public function getGroupNames(): ?array;

    /**
     * Get Group Collection
     *
     * @return PersistentCollection|null
     */
    public function getGroups(): ?PersistentCollection;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasGroup(string $name): bool;

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group): self;

    /**
     * @param GroupInterface $group
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group): self;
}
