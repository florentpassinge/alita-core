<?php

declare(strict_types = 1);

namespace App\Form\Login;

use App\Validator\Password;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ResetPasswordType.
 */
class ResetPasswordType extends AbstractType
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param FormBuilderInterface<mixed> $builder
     * @param array<string, mixed>        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => $this->translator->trans('error.form.field.password.notSimilar', [], 'error'),
                'options'         => ['attr' => ['class' => 'password-field']],
                'required'        => true,
                'first_options'   => [
                    'label'              => 'form.label.password',
                    'translation_domain' => 'form',
                    'required'           => true,
                    'attr'               => [
                        'class' => 'js-password',
                    ],
                    'constraints' => [
                        new NotBlank(),
                        new Password(),
                    ],
                ],
                'second_options' => [
                    'label'              => 'form.label.passwordConfirm',
                    'translation_domain' => 'form',
                    'required'           => true,
                    'attr'               => [
                        'class'        => 'js-confirm',
                        'data-confirm' => 'reset_password_password_first',
                    ],
                    'constraints' => [
                        new NotBlank(),
                    ],
                ],
            ])
            ->add('submit', SubmitType::class,
                [
                    'label'              => 'form.button.updatepassword',
                    'translation_domain' => 'form',
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'reset_item',
        ]);
    }
}
