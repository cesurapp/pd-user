<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Configuration;

/**
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
interface ConfigInterface
{
    public function get(string $name);
}
