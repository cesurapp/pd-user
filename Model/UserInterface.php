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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Security\Core\User\UserInterface as BaseUserInterface;

/**
 * User Interface.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface UserInterface extends BaseUserInterface
{
    public function getId(): int;

    public function setUserIdentifier(?string $username): self;
    public function setPassword(string $password): self;

    public function getEmail(): ?string;
    public function setEmail(string $email): self;

    public function isActive(): bool;
    public function setActive(bool $enabled): self;

    public function isFreeze(): bool;
    public function setFreeze(bool $enabled): self;

    public function getLastLogin(): ?\DateTime;
    public function setLastLogin(\DateTime $time = null): self;

    public function getConfirmationToken(): ?string;
    public function setConfirmationToken(?string $confirmationToken): self;
    public function createConfirmationToken(): self;

    public function getPasswordRequestedAt(): ?\DateTime;
    public function setPasswordRequestedAt(?\DateTime $date): self;
    public function isPasswordRequestNonExpired($ttl): bool;

    public function getCreatedAt(): ?\DateTime;
    public function setCreatedAt(?\DateTime $date): self;

    public function getRoles(bool $privateRoles = false): ?array;
    public function setRoles(array $roles): self;
    public function addRole(string $role): self;
    public function removeRole(string $role): self;
    public function hasRole(string $role): bool;

    public function getGroups(): null|ArrayCollection|PersistentCollection;
    public function setGroups(null|ArrayCollection|PersistentCollection $groups): self;
    public function getGroupNames(): ?array;
    public function hasGroup(string $name): bool;
    public function addGroup(GroupInterface $group): self;
    public function removeGroup(GroupInterface $group): self;

    public function getFirstName(): ?string;
    public function setFirstName(?string $firstname): self;
    public function getLastName(): ?string;
    public function setLastName(?string $lastname): self;
    public function getFullName(): ?string;
    public function getPhone(): ?string;
    public function setPhone(?string $phone): self;
    public function getLanguage(): ?string;
    public function setLanguage(?string $language): self;
}
