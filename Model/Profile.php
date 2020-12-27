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
 * User Profile.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class Profile implements ProfileInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(min="3", max="50")
     */
    protected $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     * @Assert\Length(min="3", max="50")
     */
    protected $lastName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=true)
     * @Assert\Length(min="6", max="15")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Url()
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     * @Assert\Length(min="2", max="100")
     */
    protected $company;

    /**
     * @var string
     *
     * @ORM\Column( type="string", length=3, nullable=true)
     * @Assert\Language()
     */
    protected $language;

    public function __construct()
    {
        $this->firstName = 'User';
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstname;
    }

    public function setFirstName(?string $firstname): ProfileInterface
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastname;
    }

    public function setLastName(?string $lastname): ProfileInterface
    {
        $this->lastname = $lastname;

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

    public function setPhone(?string $phone): ProfileInterface
    {
        $this->phone = $phone;

        return $this;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): ProfileInterface
    {
        $this->website = $website;

        return $this;
    }

    public function getCompany(): ?string
    {
        return $this->company;
    }

    public function setCompany(?string $company): ProfileInterface
    {
        $this->company = $company;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): ProfileInterface
    {
        $this->language = $language;

        return $this;
    }

    public function __toString(): ?string
    {
        return $this->getFullName();
    }
}
