<?php

/**
 * This file is part of the pd-admin pd-user package.
 *
 * @package     pd-user
 * @license     LICENSE
 * @author      Ramazan APAYDIN <apaydin541@gmail.com>
 * @link        https://github.com/appaydin/pd-user
 */

namespace Pd\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Type;

/**
 * User Register Form.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'security.email',
                'attr' => [
                    'placeholder' => 'security.email'
                ]
            ])
            ->add('plainPassword', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'first_options' => [
                    'label' => 'security.password',
                    'attr' => [
                        'placeholder' => 'security.password'
                    ]
                ],
                'second_options' => [
                    'label' => 'security.password_confirmation',
                    'attr' => [
                        'placeholder' => 'security.password_confirmation'
                    ]
                ],
                'constraints' => [
                    new Length([
                        'min' => 3,
                        'max' => 4096,
                    ]),
                ],
                'invalid_message' => 'password_dont_match',
            ]);

        // Add Profile
        /*$builder->add($builder
            ->create('profile', FormType::class, [
                'data_class' => $options['profile_class'],
                'label' => false,
                'attr' => ['class' => 'col-12'],
            ])
            ->add('firstname', TextType::class, [
                'label_attr' => ['style' => 'display:none'],
                'label' => false,
                'attr' => ['placeholder' => 'firstname'],
            ])
            ->add('lastname', TextType::class, [
                'label_attr' => ['style' => 'display:none'],
                'label' => false,
                'attr' => ['placeholder' => 'lastname'],
            ])
            ->add('phone', TextType::class, [
                'label_attr' => ['style' => 'display:none'],
                'label' => false,
                'attr' => ['placeholder' => 'phone'],
                'required' => false,
                'constraints' => [
                    new Type([
                        'type' => 'numeric',
                    ]),
                ],
            ])
        );*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('profile_class');
    }
}
