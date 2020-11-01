<?php

declare(strict_types = 1);

namespace Alita\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class PasswordValidator.
 */
class PasswordValidator extends ConstraintValidator
{
    protected TranslatorInterface $translator;

    protected int $minLength;
    protected int $maxLength;

    public function __construct(TranslatorInterface $translator, int $formPasswordMinChar, int $formPasswordMaxChar)
    {
        $this->maxLength  = $formPasswordMaxChar;
        $this->minLength  = $formPasswordMinChar;
        $this->translator = $translator;
    }

    public function validate($password, Constraint $constraint): void
    {
        /** @var Password $constraint */
        if (null === $password && $constraint->authorizedNull) {
            return;
        }

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

        if (strlen($password) < $this->minLength || strlen($password) > $this->maxLength) {
            $this->context->buildViolation(
                    $this->translator->trans($constraint->messageFieldLength,
                        ['%field%' => 'Mot de passe', '%min%' => $this->minLength, '%max%' => $this->maxLength],
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
