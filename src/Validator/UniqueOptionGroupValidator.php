<?php

namespace App\Validator;

use App\Entity\ProductVariant;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class UniqueOptionGroupValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$value instanceof ProductVariant) {
            return;
        }
        $groups = [];
        foreach ($value->getProductVariantOptions() as $option) {
            $groupId = $option->getOptionValue()->getOptionGroup()->getId();
            if (isset($groups[$groupId])) {
                $this->context->buildViolation($constraint->message)
                    ->addViolation();
                return;
            }
            $groups[$groupId] = true;
        }
    }
}
