# pdUser Bundle
Simple user management system for Symfony 5+.

[![Packagist](https://img.shields.io/packagist/dt/appaydin/pd-user.svg)](https://github.com/appaydin/pd-user)
[![Github Release](https://img.shields.io/github/release/appaydin/pd-user.svg)](https://github.com/appaydin/pd-user)
[![license](https://img.shields.io/github/license/appaydin/pd-user.svg)](https://github.com/appaydin/pd-user)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/appaydin/pd-user.svg)](https://github.com/appaydin/pd-user)

Installation
---

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require appaydin/pd-user
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

With Symfony 5, the package will be activated automatically. But if something goes wrong, you can install it manually.

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
<?php
// config/bundles.php

return [
    //...
    Pd\UserBundle\PdUserBundle::class => ['all' => true]
];
```

### Step 3: Create User, Group, Class
##### A) Create User Class
Create the User class for your application. This class can look and act however you want: add any properties or methods you find useful. This is your User class.
```php
<?php
// src/Entity/User.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Pd\UserBundle\Model\User as BaseUser;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity
 * @UniqueEntity(fields="email", message="email_already_taken")
 */
class User extends BaseUser
{
    public function __construct()
    {
        parent::__construct();
    }
}
```

##### B) Create Group Class
Create the Group class for your application. This class creates user groups.
```php
<?php
// src/Entity/Group.php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Pd\UserBundle\Model\Group as BaseGroup;

/**
 * @ORM\Table(name="user_group")
 * @ORM\Entity
 * @UniqueEntity(fields="name", message="group_already_taken")
 */
class Group extends BaseGroup
{

}
```

### Step 4: Settings Bundle
Create a "user.yaml" file for the settings.
```yaml
# config/packages/user.yaml

pd_user:
    user_class: App\Entity\User
    group_class: App\Entity\Group
    default_group: ''
    login_redirect: 'web_home'
    email_confirmation: true
    welcome_email: true
    user_registration: true
    #template_path: '@Admin/Auth'
    resetting_request_time: 7200
    mail_sender_address: 'example@example.com'
    mail_sender_name: 'pdUser'
    active_language: ['en', 'tr']
    register_type: ''
    resetting_type: ''

```
* __user_class:__ Define 'User' class address
* __group_class:__ Define 'Group' class address
* __default_group:__ New members will join group id
* __login_redirect:__ The router name to which logged-in users will be directed.
* __email_confirmation:__ Enables email verification for register.
* __welcome_email:__ Welcome new members welcome message.
* __user_registration:__ Enable/Disable user registration.
* __template_path:__ Directory for Twig templates. Changes can be made by copying the source directory.
* __resetting_request_time:__ Enter the retry time in seconds for password renewal.
* __mail_sender_address:__ Mail sender address
* __mail_sender_name:__ Mail sender name
* __active_language:__ List of Active Language
* __register_type:__ Register Form Type
* __resetting_type:__ Resetting Form Type

####Events
    UserEvent::REGISTER_BEFORE => It is launched before the registration form.
    UserEvent::REGISTER => When the recording is complete, it is launched.
    UserEvent::REGISTER_CONFIRM => E-mail is launched when registration is confirmed.
    UserEvent::RESETTING => The password is launched when the reset mail is sent.
    UserEvent::RESETTING_COMPLETE => It is launched when the password is changed.

### Step 5: Configure Your Application's Security.yml
Below is a minimal example of the configuration necessary to use the pdUser in your application:
```yaml
# config/packages/security.yaml

security:
    enable_authenticator_manager: true
    password_hashers:
        App\Entity\User:
            algorithm: auto
    role_hierarchy:
        ROLE_ADMIN: [ROLE_USER]
    providers:
        pdadmin_auth:
            entity:
                class: App\Entity\User
                property: email
    firewalls:
        # Enable for Development
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false
        main:
            pattern:    ^/
            provider: pdadmin_auth
            lazy: true
            user_checker: Pd\UserBundle\Security\UserChecker
            switch_user: true
            http_basic: ~
            entry_point: form_login
            form_login:
                use_referer: true
                login_path: security_login
                check_path: security_login
                #default_target_path: 'dashboard' # Login Redirect Path
            logout:
                path: security_logout
                #target: 'home' # Logout Redirect Path
            remember_me:
                secret:   '%env(APP_SECRET)%'
                #lifetime: 604800
                path:     /
    access_control:
        - { path: ^/auth/login$, role: PUBLIC_ACCESS }
        - { path: ^/auth/register, role: PUBLIC_ACCESS }
        - { path: ^/auth/resetting, role: PUBLIC_ACCESS }
        #- { path: '^/', role: ROLE_ADMIN }
```

### Step 6: Import pdUser Routing
Now that you have activated and configured the bundle, all that is left to do is import the pdUser routing files.

By importing the routing files you will have ready made pages for things such as logging in, register, password resetting.
```yaml
#config/routes.yaml

authorization:
    resource: "@PdUserBundle/Resources/config/routing.yaml"
    prefix: 'auth'
```

### Step 6: Update Your Database Schema
All steps are completed. You can now update the database schema.
```yaml
php bin/console doctrine:schema:update --force
```
