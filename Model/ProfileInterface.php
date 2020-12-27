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
 * User Profile Data.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface ProfileInterface
{
    /**
     * Get id.
     */
    public function getId(): int;

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstName(): ?string;

    /**
     * Set firstname.
     *
     * @return $this
     */
    public function setFirstName(?string $firstname): self;

    /**
     * @return string
     */
    public function getLastName(): ?string;

    /**
     * Set Last Name.
     *
     * @return $this
     */
    public function setLastName(?string $lastname): self;

    /**
     * Get Full Name.
     */
    public function getFullName(): ?string;

    /**
     * @return string
     */
    public function getPhone(): ?string;

    /**
     * @return $this
     */
    public function setPhone(?string $phone): self;

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite(): ?string;

    /**
     * @return $this
     */
    public function setWebsite(?string $website): self;

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany(): ?string;

    /**
     * @return $this
     */
    public function setCompany(string $company): self;

    /**
     * @return string
     */
    public function getLanguage(): ?string;

    /**
     * @return $this
     */
    public function setLanguage(?string $language): self;
}
