<?php

declare(strict_types = 1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * Class Password.
 */
class Password extends Constraint
{
    public string $messageFieldContains = 'error.form.field.contains';

    public string $messageFieldLength = 'error.form.field.length';

    public string $messageVarNotExist = 'error.form.field.notfound';

    public bool $authorizedNull;

    public function __construct(bool $authorizedNull = false, $options = null)
    {
        parent::__construct($options);
        $this->authorizedNull = $authorizedNull;
    }
}
