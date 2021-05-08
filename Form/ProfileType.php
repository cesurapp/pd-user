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

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Intl\Languages;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Type;

/**
 * User Profile Type.
 *
 * @author Ramazan APAYDIN <apaydin541@gmail.com>
 */
class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add Email
        $builder
            ->add('email', EmailType::class, [
                'label' => 'security.email',
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
                    new Type(['type' => 'numeric']),
                ],
            ])
            ->add('language', ChoiceType::class, [
                'label' => 'language',
                'choices' => array_flip(array_intersect_key(Languages::getNames(), array_flip($options['active_language']))),
                'choice_translation_domain' => false,
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'save',
            ]);
    }

    /**
     * Set Default Options.
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('active_language');
    }

    public function getBlockPrefix(): string
    {
        return '';
    }
}
