<?php

declare(strict_types = 1);

namespace Alita\Validator;

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
        /** @var CheckEntity $checkEntity */
        $checkEntity = clone $constraint;
        $entity      = $this->em->getRepository($checkEntity->entityName)
            ->findOneBy(array_merge(
                [$checkEntity->fieldName => $value],
                $checkEntity->extraField,
            ));

        if (null === $entity) {
            $this->context
                ->buildViolation($this->translator->trans(
                    $checkEntity->errorMessage,
                    [],
                    'error'
                ))
                ->addViolation();
        }
    }
}
