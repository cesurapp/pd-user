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

namespace Pd\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * User Password Resetting Form
 *
 * @author Kerem APAYDIN <kerem@apaydin.me>
 */
class ResettingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username', EmailType::class, [
                'attr' => ['placeholder' => 'security.login_username'],
                'label_attr' => ['style' => 'display:none'],
                'label' => false
            ]);
    }
}
