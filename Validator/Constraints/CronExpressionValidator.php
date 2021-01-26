<?php

namespace JMose\CommandSchedulerBundle\Validator\Constraints;

use Cron\CronExpression as CronExpressionLib;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class CronExpressionValidator.
 */
class CronExpressionValidator extends ConstraintValidator
{
    /**
     * Validate method for CronExpression constraint.
     * TODO check
     *
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate(mixed $value, Constraint $constraint): void
    {
        $value = (string) $value;

        if ('' === $value) {
            return;
        }

        try {
           new CronExpressionLib($value);
        } catch (\InvalidArgumentException) {
            $this->context->addViolation($constraint->message, []);
        }
    }
}
