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
     * @ORM\Column(name="firstname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="50")
     */
    protected $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="50")
     */
    protected $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=15, nullable=true)
     * @Assert\Length(min="7", max="14")
     */
    protected $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=100, nullable=true)
     * @Assert\Url()
     */
    protected $website;

    /**
     * @var string
     *
     * @ORM\Column(name="company", type="string", length=100, nullable=true)
     * @Assert\Length(min="2", max="100")
     */
    protected $company;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=3, nullable=true)
     * @Assert\Language()
     */
    protected $language;

    /**
     * Get id.
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    /**
     * Set firstname.
     *
     * @param string $firstname
     *
     * @return Profile
     */
    public function setFirstname(string $firstname): ProfileInterface
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get lastname.
     *
     * @return string
     */
    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    /**
     * Set lastname.
     *
     * @param string $lastname
     *
     * @return Profile
     */
    public function setLastname(string $lastname): ProfileInterface
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get Fullname.
     */
    public function getFullName(): string
    {
        return $this->firstname . ' ' . $this->lastname;
    }

    /**
     * Get phone.
     *
     * @return string
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set phone.
     *
     * @param string $phone
     *
     * @return Profile
     */
    public function setPhone(string $phone): ProfileInterface
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * Set website.
     *
     * @param string $website
     *
     * @return Profile
     */
    public function setWebsite(string $website): ProfileInterface
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany(): ?string
    {
        return $this->company;
    }

    /**
     * Set company.
     *
     * @param string $company
     *
     * @return Profile
     */
    public function setCompany(string $company): ProfileInterface
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get language.
     *
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * Set language.
     *
     * @param string $language
     *
     * @return Profile
     */
    public function setLanguage(string $language): ProfileInterface
    {
        $this->language = $language;

        return $this;
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
