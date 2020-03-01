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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User Group.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Group implements GroupInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", unique=true, length=180)
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="180")
     */
    protected $name;

    /**
     * @ORM\Column(type="array")
     */
    protected $roles;

    public function __construct($name, $roles = [])
    {
        $this->name = $name;
        $this->roles = $roles;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return $this
     */
    public function setName(string $name): GroupInterface
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @return $this
     */
    public function setRoles(array $roles): GroupInterface
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return $this
     */
    public function addRole(string $role): GroupInterface
    {
        if (!$this->hasRole($role)) {
            $this->roles[] = mb_strtoupper($role);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function removeRole(string $role): GroupInterface
    {
        if (false !== $key = array_search(mb_strtoupper($role), $this->roles, true)) {
            unset($this->roles[$key]);
            $this->roles = array_values($this->roles);
        }

        return $this;
    }

    /**
     * @param $role
     */
    public function hasRole(string $role): bool
    {
        return \in_array(mb_strtoupper($role), $this->roles, true);
    }
}
