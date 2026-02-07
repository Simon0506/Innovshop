<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class SelectedOptionValuesUniqueGroupValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!is_iterable($value)) {
            return;
        }

        $groups = [];

        /** @var OptionValue $optionValue */
        foreach ($value as $optionValue) {
            $groupId = $optionValue->getOptionGroup()->getId();
            if (isset($groups[$groupId])) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->addViolation();
                return;
            }
            $groups[$groupId] = true;
        }
    }
}
