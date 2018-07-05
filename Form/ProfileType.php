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

use Pd\UserBundle\Model\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Intl\Intl;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Profile Form Type.
 *
 * @author  Ramazan Apaydın <iletisim@ramazanapaydin.com>
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add Email
        $builder->add('email', EmailType::class, [
                'label' => 'security.email',
            ]
        );

        // Add Profile Section
        $builder->add(
            $builder
                ->create('profile', FormType::class, [
                    'label' => false,
                    'attr' => ['class' => 'col-12'],
                    'data_class' => $options['profile_class'],
                ])
                ->add('firstname', TextType::class, [
                    'label' => 'firstname',
                ])
                ->add('lastname', TextType::class, [
                    'label' => 'lastname',
                ])
                ->add('phone', TextType::class, [
                    'label' => 'phone',
                    'required' => false,
                    'constraints' => [
                        new Type(['type' => 'numeric'])
                    ]
                ])
                ->add('website', TextType::class, [
                    'label' => 'website',
                    'required' => false,
                ])
                ->add('company', TextType::class, [
                    'label' => 'company',
                    'required' => false,
                ])
                ->add('language', ChoiceType::class, [
                    'label' => 'language',
                    'choices' => $this->getLanguageList($options['container']),
                    'choice_translation_domain' => false
                ])
        );

        // Add Admin Item
        $this->setAdminItem($builder, $options['container']);

        // Add Submit
        $builder->add('submit', SubmitType::class, [
            'label' => 'save',
        ]);
    }

    /**
     * Set Default Options.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired('data_class')
            ->setRequired('profile_class')
            ->setRequired('container');
    }

    /**
     * Add Admin Item.
     *
     * @param FormBuilderInterface $builder
     * @param $options
     */
    public function setAdminItem(FormBuilderInterface &$builder, ContainerInterface $container)
    {
        if ($container->get('security.authorization_checker')->isGranted(User::ROLE_ALL_ACCESS)) {
            $builder
                ->add('createdAt', DateTimeType::class, [
                    'label' => 'created_at',
                    'format' => 'yyyy-MM-dd HH:mm',
                    'widget' => 'single_text',
                    'html5' => true,
                    'attr' => ['data-picker' => 'datetime'],
                ]);
        }
    }

    /**
     * Return Active Language List.
     *
     * @param ContainerInterface $container
     *
     * @return array|bool
     */
    public function getLanguageList(ContainerInterface $container)
    {
        $allLangs = Intl::getLanguageBundle()->getLanguageNames();

        return array_flip(array_intersect_key($allLangs, array_flip($container->getParameter('pd_user.active_language'))));
    }
}
