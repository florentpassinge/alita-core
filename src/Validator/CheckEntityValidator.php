<?php

declare(strict_types = 1);

namespace App\Validator;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CheckEntityValidator.
 */
class CheckEntityValidator extends ConstraintValidator
{
    protected EntityManagerInterface $em;
    protected TranslatorInterface $translator;

    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator)
    {
        $this->em         = $em;
        $this->translator = $translator;
    }

    public function validate($value, Constraint $constraint): void
    {
        /** @var CheckEntity $constraint */
        $entity = $this->em->getRepository($constraint->entityName)
            ->findOneBy(array_merge(
                [$constraint->fieldName => $value],
                $constraint->extraField,
            ));

        if (null === $entity) {
            $this->context
                ->buildViolation($this->translator->trans(
                    $constraint->errorMessage,
                    [],
                    'error'
                ))
                ->addViolation();
        }
    }
}
