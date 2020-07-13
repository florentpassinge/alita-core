<?php

declare(strict_types = 1);

namespace Alita\Form\Login;

use Alita\Entity\User;
use Alita\Validator\CheckEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class ForgotPasswordType.
 */
class ForgotPasswordType extends AbstractType
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
            ->add('email', EmailType::class, [
                'required'           => true,
                'label'              => 'form.label.email',
                'translation_domain' => 'form',
                'constraints'        => [
                    new Email(),
                    new CheckEntity(User::class, 'email', 'error.form.entity.notfound.user', [
                        'active' => true,
                    ]),
                ],
            ])
            ->add('submit', SubmitType::class,
                [
                    'label'              => 'form.button.send',
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
