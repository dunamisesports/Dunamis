<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('firstName', TextType::class)
            ->add('email', EmailType::class)
            ->add('roles', ChoiceType::class,
                [
                    'choices'       => [
                        'Konclave'         => 'ROLE_SUPER_ADMIN',
                        'Communication'    => 'ROLE_AUTHOR',
                        'Membre'           => 'ROLE_USER'
                    ]
                ])
        ;
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesAsArray)
                {
                    return implode(',', $rolesAsArray);
                },
                function ($rolesAsString)
                {
                    return explode(',', $rolesAsString);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
