<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice;

use Stadly\PasswordPolice\Rule\RuleException;

/**
 * Interface that must be implemented by all rules.
 */
interface Rule
{
    /**
     * Check whether a password is in compliance with the rule.
     *
     * @param Password|string $password Password to check.
     * @param int|null $weight Don't consider constraints with lower weights.
     * @return bool Whether the password is in compliance with the rule.
     * @throws RuleException If an error occurred.
     */
    public function test($password, ?int $weight = 1): bool;

    /**
     * Validate that a password is in compliance with the rule.
     *
     * @param Password|string $password Password to validate.
     * @return ValidationError|null Validation error describing why the password is not in compliance with the rule.
     * @throws RuleException If an error occurred.
     */
    public function validate($password): ?ValidationError;
}
