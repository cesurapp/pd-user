<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 *
 * @license     LICENSE
 * @author      Kerem APAYDIN <kerem@apaydin.me>
 *
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Model;

/**
 * User Profile Data
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
interface ProfileInterface
{
    /**
     * Get id.
     *
     * @return int
     */
    public function getId(): int;

    /**
     * Get firstname.
     *
     * @return string
     */
    public function getFirstname(): ?string;

    /**
     * Set firstname.
     *
     * @param string $firstname
     *
     * @return $this
     */
    public function setFirstname(string $firstname): self;

    /**
     * @return string
     */
    public function getLastname(): ?string;

    /**
     * @param string $lastname
     *
     * @return $this
     */
    public function setLastname(string $lastname): self;

    /**
     * @return string
     */
    public function getFullName(): string;

    /**
     * @return string
     */
    public function getPhone(): ?string;

    /**
     * @param int $phone
     *
     * @return $this
     */
    public function setPhone(string $phone): self;

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite(): ?string;

    /**
     * @param string $website
     *
     * @return $this
     */
    public function setWebsite(string $website): self;

    /**
     * Get company.
     *
     * @return string
     */
    public function getCompany(): ?string;

    /**
     * @param string $company
     *
     * @return $this
     */
    public function setCompany(string $company): self;

    /**
     * @return string
     */
    public function getLanguage(): ?string;

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage(string $language): self;
}
