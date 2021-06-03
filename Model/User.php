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
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * User Account.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface, \Serializable
{
    public const ROLE_DEFAULT = 'ROLE_USER';
    public const ROLE_ALL_ACCESS = 'ROLE_SUPER_ADMIN';

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $password;

    /**
     * @ORM\Column(type="string", length=100, unique=true, nullable=true)
     * @Assert\Email()
     */
    protected $email;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $active;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $freeze;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $lastLogin;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=true)
     * @Assert\Length(max=180)
     */
    protected $confirmationToken;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $passwordRequestedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
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

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="2", max="255")
     */
    protected $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(min="2", max="255")
     */
    protected $lastName;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Assert\Length(max=20)
     */
    protected $phone;

    /**
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Assert\Language()
     */
    protected $language;

    public function __construct()
    {
        $this->active = true;
        $this->freeze = false;
        $this->roles = [static::ROLE_DEFAULT];
        $this->createdAt = new \DateTime();
        $this->groups = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->email;
    }

    public function setUserIdentifier(?string $username): UserInterface
    {
        $this->email = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): UserInterface
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): UserInterface
    {
        $this->email = $email;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $enabled): UserInterface
    {
        $this->active = $enabled;

        return $this;
    }

    public function isFreeze(): bool
    {
        return $this->freeze;
    }

    public function setFreeze(bool $enabled): UserInterface
    {
        $this->freeze = $enabled;

        return $this;
    }

    public function getLastLogin(): ?\DateTime
    {
        return $this->lastLogin;
    }

    public function setLastLogin(\DateTime $time = null): UserInterface
    {
        $this->lastLogin = $time;

        return $this;
    }

    public function getConfirmationToken(): ?string
    {
        return $this->confirmationToken;
    }

    public function setConfirmationToken(?string $confirmationToken): UserInterface
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    public function createConfirmationToken(): UserInterface
    {
        $this->confirmationToken = rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');

        return $this;
    }

    public function getPasswordRequestedAt(): ?\DateTime
    {
        return $this->passwordRequestedAt;
    }

    public function setPasswordRequestedAt(?\DateTime $date): UserInterface
    {
        $this->passwordRequestedAt = $date;

        return $this;
    }

    public function isPasswordRequestNonExpired($ttl): bool
    {
        return $this->getPasswordRequestedAt() instanceof \DateTime && $this->getPasswordRequestedAt()->getTimestamp() + $ttl > time();
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $date): UserInterface
    {
        $this->createdAt = $date;

        return $this;
    }

    public function getRoles(bool $privateRoles = false): ?array
    {
        if ($privateRoles) {
            return $this->roles;
        }

        $roles = $this->roles;
        $groupRoles = [[]];

        foreach ($this->getGroups() as $group) {
            $groupRoles[] = $group->getRoles();
        }
        $groupRoles = array_merge(...$groupRoles);

        return array_unique(array_merge($roles, $groupRoles));
    }

    public function setRoles(array $roles): UserInterface
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRoles(array $roles): UserInterface
    {
        $this->roles = [];

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    public function addRole(string $role): UserInterface
    {
        $role = mb_strtoupper($role);

        if (!\in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): UserInterface
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return \in_array(mb_strtoupper($role), $this->getRoles(), true);
    }

    public function getGroups(): null|PersistentCollection|ArrayCollection
    {
        return $this->groups;
    }

    public function setGroups($groups): UserInterface
    {
        $this->groups = $groups;

        return $this;
    }

    public function getGroupNames(): ?array
    {
        return $this->getGroups()
            ->map(function (Group $group) {
                return $group->getName();
            })
            ->toArray();
    }

    public function hasGroup(string $name): bool
    {
        return \in_array($name, $this->getGroupNames(), true);
    }

    public function addGroup(GroupInterface $group): UserInterface
    {
        if (!$this->getGroups()->contains($group)) {
            $this->getGroups()->add($group);
        }

        return $this;
    }

    public function removeGroup(GroupInterface $group): UserInterface
    {
        if ($this->getGroups()->contains($group)) {
            $this->getGroups()->removeElement($group);
        }

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstname): UserInterface
    {
        $this->firstName = $firstname;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastname): UserInterface
    {
        $this->lastName = $lastname;

        return $this;
    }

    public function getFullName(): ?string
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): UserInterface
    {
        $this->phone = $phone;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): UserInterface
    {
        $this->language = $language;

        return $this;
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername(): ?string
    {
        return $this->getUserIdentifier();
    }

    public function serialize(): ?string
    {
        return serialize([
            $this->id,
            $this->password,
            $this->getUserIdentifier(),
            $this->active,
            $this->lastLogin,
            $this->createdAt,
        ]);
    }

    public function unserialize($data)
    {
        $data = unserialize($data);

        $this->id = $data[0];
        $this->password = $data[1];
        $this->setUserIdentifier($data[2]);
        $this->active = $data[3];
        $this->lastLogin = $data[4];
        $this->createdAt = $data[5];
    }
}
