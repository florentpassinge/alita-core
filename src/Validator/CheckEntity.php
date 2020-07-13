<?php

declare(strict_types = 1);

namespace Alita\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckEntity.
 */
class CheckEntity extends Constraint
{
    public string $entityName;
    /** @var array<string, mixed> $extraField */
    public array $extraField;
    public string $fieldName;
    public string $errorMessage = 'error.form.entity.notfound.entity';

    /** @param array<string, mixed> $extraField */
    public function __construct(
        string $entityName,
        string $fieldName,
        ?string $translate = null,
        array $extraField = [],
        $options = null
    ) {
        parent::__construct($options);
        $this->entityName = $entityName;
        $this->extraField = $extraField;
        $this->fieldName  = $fieldName;

        if (null !== $translate) {
            $this->errorMessage = $translate;
        }
    }
}
