<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;

class SigninType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'E-mail',
                'attr' => [
                    'placeholder' => 'Saisir votre e-mail.'
                ]
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'attr' => [
                    'placeholder' => 'Saisir votre mot de passe.',

                ]
            ])
            // ->add('roles')
            ->add('nom', TextType::class, [
            ])
            ->add('prenom')
            ->add('activate')
            ->add('submit', SubmitType::class, [
                'label' => 'Valider'
            ])
            // ->add('captcha', ReCaptcha3Type::class, [
            //     'mapped' => false
            // ])
            ->add('captcha', Recaptcha3Type::class, [ 
                'constraints' => new Recaptcha3(), 
                'action_name' => 'register', 
                'locale' => 'fr', 
                ]) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
