<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Karser\Recaptcha3Bundle\Form\Recaptcha3Type; 
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3; 

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Votre email ...",
                    "class" => "form-control"
                ],
                "row_attr" => [
                    "class" => "form-group col-md-12 mb-3"
                ],
                "required" => true,
            ])
            ->add('password', RepeatedType::class, [
               // instead of being set onto the object directly,
                    // this is read and encoded in the controller
                    'type' => PasswordType::class,
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez entrer un mot de passe.',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit comporter au moins {{ limit }} caractères.',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ])
                    ],
                    'first_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Entre votre mot de passe',
                            'class' => 'form-control'
                        ],
                        'row_attr' => [
                            'class' => 'form-group mb-3'
                        ]
                    ],
                    'second_options' => [
                        'label' => false,
                        'attr' => [
                            'placeholder' => 'Confirmation mot de passe',
                            'class' => 'form-control'
                        ],
                        'row_attr' => [
                            'class' => 'form-group mb-3'
                        ]
                    ]
                ])
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entre votre nom',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'form-group mb-3'
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => false,
                'attr' => [
                    'placeholder' => 'Entre votre premon',
                    'class' => 'form-control'
                ],
                'row_attr' => [
                    'class' => 'form-group mb-3'
                ]
            ])
            ->add('register', SubmitType::class, [
                "label" => "Insciption",
                "attr" => [
                    "placeholder" => "Insciption",
                    "class" => "btn btn-fill-out btn-block",
                    "name" => "register",
                    "type" => "submit",
                ],
                "row_attr" => [
                    "class" => "col-md-12"
                ],

            ])
            ->add('captcha', Recaptcha3Type::class, [ 
                'constraints' => new Recaptcha3(), 
                'action_name' => 'register', 
                'locale' => 'fr', 
                ]) ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
