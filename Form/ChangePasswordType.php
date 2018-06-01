<?php

/**
 * This file is part of the pdAdmin pdUser package.
 *
 * @package     pdUser
 *
 * @author      Ramazan APAYDIN <iletisim@ramazanapaydin.com>
 * @copyright   Copyright (c) 2018 Ramazan APAYDIN
 * @license     LICENSE
 *
 * @link        https://github.com/rmznpydn/pd-user
 */

namespace Pd\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;

/**
 * Registration Form Type.
 *
 * @author  Ramazan Apaydın <iletisim@ramazanapaydin.com>
 */
class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class, [
                'label' => 'security.password_current',
                'mapped' => false,
                'constraints' => new UserPassword(),
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => ['label' => 'security.password'],
                'second_options' => ['label' => 'security.password_confirmation'],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'password_dont_match',
            ])
            ->add('Submit', SubmitType::class, [
                'label' => 'save',
            ]);

        // Remove Current Password
        if ($options['disable_current_password']) {
            $builder->remove('current_password');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('data_class')
            ->setRequired('disable_current_password');
    }
}
