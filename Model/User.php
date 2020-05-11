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

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Account.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class User implements UserInterface, \Serializable
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_ALL_ACCESS = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Profile", cascade={"persist", "merge", "remove"})
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     * @Assert\Valid()
     */
    protected $profile;

    /**
     * @ORM\Column(type="string", length=98)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    protected $isActive;

    /**
     * @ORM\Column(name="is_freeze", type="boolean")
     */
    protected $isFreeze;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(name="last_login_ip", type="string", length=32, nullable=true)
     */
    protected $lastLoginIp;

    /**
     * @ORM\Column(name="confirmation_token", type="string", length=180, unique=true, nullable=true)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(name="password_requested_at", type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=true)
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    /**
     * @ORM\ManyToMany(targetEntity="Group")
     * @ORM\JoinTable(name="user_group_tax",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $groups;

    public function __construct()
    {
        $this->isActive = true;
        $this->isFreeze = false;
        $this->roles = [static::ROLE_DEFAULT];
        $this->createdAt = new \DateTime();
        $this->groups = new ArrayCollection();
    }

    public function getSalt()
    {
        return null;
    }

    public function __toString()
    {
        return $this->getUsername();
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return ProfileInterface
     */
    public function getProfile(): ?ProfileInterface
    {
        return $this->profile;
    }

    /**
     * @return $this
     */
    public function setProfile(ProfileInterface $profile): UserInterface
    {
        $this->profile = $profile;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    /**
     * @param $username
     *
     * @return $this
     */
    public function setUsername(string $username): UserInterface
    {
        $this->email = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param $password
     *
     * @return $this
     */
    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param $email
     *
     * @return $this
     */
    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->isActive;
    }

    /**
     * @param $enabled bool
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): UserInterface
    {
        $this->isActive = $enabled;

        return $this;
    }

    public function isFreeze(): bool
    {
        return $this->isFreeze;
    }

    /**
     * @param $enabled bool
     *
     * @return $this
     */
    public function setFreeze(bool $enabled): UserInterface
    {
        $this->isFreeze = $enabled;

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @return $this
     */
    public function setLastLogin(\DateTime $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    /**
     * Get Last Login IP.
     */
    public function getLastLoginIp(): ?string
    {
        return $this->lastLoginIp;
    }

    /**
     * Set Last Login IP.
     */
    public function setLastLoginIp(?string $lastLoginIp): UserInterface
    {
        $this->lastLoginIp = $lastLoginIp;

        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    /**
     * @param string $confirmationToken
     *
     * @return $this
     */
    public function setConfirmationToken(?string $confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * @return $this
     */
    public function createConfirmationToken(): UserInterface
    {
        $this->confirmationToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    /**
     * @return $this
     */
    public function setPasswordRequestedAt(\DateTime $date = null): UserInterface
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    /**
     * @param $ttl
     */
    public function isPasswordRequestNonExpired($ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return $this
     */
    public function setCreatedAt(\DateTime $time = null): UserInterface
    {
        $this->createdAt = $time;

        return $this;
    }

    /**
     * Group combined Roles.
     *
     * @return array
     */
    public function getRoles(): ?array
    {
        $roles = $this->roles;
        $groupRoles = [[]];

        foreach ($this->getGroups() as $group) {
            $groupRoles[] = $group->getRoles();
        }
        $groupRoles = array_merge(...$groupRoles);

        return array_unique(array_merge($roles, $groupRoles));
    }

    /**
     * @return array
     */
    public function getRolesUser(): ?array
    {
        return $this->roles;
    }

    /**
     * Change Roles.
     *
     * @return $this
     */
    public function setRoles(array $roles): UserInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Add Roles Array.
     *
     * @return $this
     */
    public function addRoles(array $roles): UserInterface
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * Add Role.
     *
     * @param $role
     *
     * @return $this
     */
    public function addRole(string $role): UserInterface
    {
        $role = mb_strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    /**
     * Remove Role.
     *
     * @param $role
     *
     * @return $this
     */
    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * Check Role.
     *
     * @param $role
     */
    public function hasRole(string $role): bool
    {
        return \in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    /**
     * Get Group List.
     *
     * @return PersistentCollection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Get Group Names.
     *
     * @return array
     */
    public function getGroupNames(): ?array
    {
        $names = [];

        foreach ($this->getGroups() as $group) {
            $names[] = $group->getName();
        }

        return $names;
    }

    /**
     * Check Group.
     *
     * @param $name
     */
    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    /**
     * Add Group.
     *
     * @return $this
     */
    public function addGroup(GroupInterface $group): UserInterface
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    /**
     * Remove Group.
     *
     * @return $this
     */
    public function removeGroup(GroupInterface $group): UserInterface
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    /**
     * Set Default ROLE.
     */
    public function eraseCredentials()
    {
    }

    public function serialize()
    {
        return serialize([
            $this->id,
            $this->password,
            $this->email,
            $this->isActive,
            $this->lastLogin,
            $this->createdAt,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->id,
            $this->password,
            $this->email,
            $this->isActive,
            $this->lastLogin,
            $this->createdAt
        ] = unserialize($serialized);
    }
}
