<?php

declare(strict_types = 1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class CheckEntity.
 */
class CheckEntity extends Constraint
{
    public string $entityName;
    public array $extraField;
    public string $fieldName;
    public string $errorMessage = 'error.form.entity.notfound.entity';

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
