<?php

declare(strict_types = 1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PasswordValidator.
 */
class PasswordValidator extends ConstraintValidator
{
    private TranslatorInterface $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function validate($password, Constraint $constraint): void
    {
        /** @var Password $constraint */
        if (null === $password && $constraint->authorizedNull) {
        } else {
            if (null === $password) {
                $this->context
                    ->buildViolation(
                        $this->translator->trans(
                            $constraint->messageVarNotExist,
                            ['%field%' => 'Mot de passe'],
                            'error')
                    )
                    ->addViolation();
            }

            if (strlen($password) < 8 || strlen($password) > 12) {
                $this->context->buildViolation(
                    $this->translator->trans($constraint->messageFieldLength,
                        ['%field%' => 'Mot de passe', '%min%' => 8, '%max%' => 12],
                        'error'))
                    ->addViolation();
            }

            if (!preg_match('/(\d)/', $password)) {
                $this->context->buildViolation(
                    $this->translator->trans(
                        $constraint->messageFieldContains,
                        ['%field%' => 'Mot de passe', '%contains%' => 'un chiffre'],
                        'error'))
                    ->addViolation();
            }

            if (!preg_match('#[a-z]+#', $password)) {
                $this->context->buildViolation(
                    $this->translator->trans(
                        $constraint->messageFieldContains,
                        ['%field%' => 'Mot de passe', '%contains%' => 'une minuscule'],
                        'error'))
                    ->addViolation();
            }
            if (!preg_match('#[A-Z]+#', $password)) {
                $this->context->buildViolation(
                    $this->translator->trans(
                        $constraint->messageFieldContains,
                        ['%field%' => 'Mot de passe', '%contains%' => 'une majuscule   '],
                        'error'))
                    ->addViolation();
            }
            if (!preg_match("#\W+#", $password)) {
                $this->context->buildViolation(
                    $this->translator->trans(
                        $constraint->messageFieldContains,
                        [
                            '%field%'    => 'Mot de passe',
                            '%contains%' => 'l\'un des caractères suivant : 
                                ` ~ ! @ # $ % ^ & * ( ) - _ = + [ { } ] \ | ; : \' " , < . > / ? € £ ¥ ₹',
                        ],
                        'error'))
                    ->addViolation();
            }
        }
    }
}
