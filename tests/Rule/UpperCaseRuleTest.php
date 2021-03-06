<?php

declare(strict_types=1);

namespace Stadly\PasswordPolice\Rule;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Stadly\PasswordPolice\ValidationError;

/**
 * @coversDefaultClass \Stadly\PasswordPolice\Rule\UpperCaseRule
 * @covers ::<protected>
 * @covers ::<private>
 */
final class UpperCaseRuleTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraint(): void
    {
        $rule = new UpperCaseRule(5, null);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(5, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMaxConstraint(): void
    {
        $rule = new UpperCaseRule(0, 10);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(0, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithBothMinAndMaxConstraint(): void
    {
        $rule = new UpperCaseRule(5, 10);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(5, 10);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithNegativeMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new UpperCaseRule(-10, null);
    }

    /**
     * @covers ::__construct
     */
    public function testCannotConstructRuleWithMaxConstraintSmallerThanMinConstraint(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $rule = new UpperCaseRule(10, 5);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructUnconstrainedRule(): void
    {
        $rule = new UpperCaseRule(0, null);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(0, null);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::__construct
     */
    public function testCanConstructRuleWithMinConstraintEqualToMaxConstraint(): void
    {
        $rule = new UpperCaseRule(5, 5);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(5, 5);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testCanAddConstraint(): void
    {
        $rule = new UpperCaseRule(5, 5, 1);
        $rule->addConstraint(10, 10, 1);

        // Force generation of code coverage
        $ruleConstruct = new UpperCaseRule(5, 5, 1);
        $ruleConstruct->addConstraint(10, 10, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::addConstraint
     */
    public function testConstraintsAreOrdered(): void
    {
        $rule = new UpperCaseRule(5, 5, 1);
        $rule->addConstraint(10, 10, 2);

        $ruleConstruct = new UpperCaseRule(10, 10, 2);
        $ruleConstruct->addConstraint(5, 5, 1);
        self::assertEquals($rule, $ruleConstruct);
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeSatisfied(): void
    {
        $rule = new UpperCaseRule(2, null);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMinConstraintCanBeUnsatisfied(): void
    {
        $rule = new UpperCaseRule(2, null);

        self::assertFalse($rule->test('foo bar'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeSatisfied(): void
    {
        $rule = new UpperCaseRule(0, 3);

        self::assertTrue($rule->test('foo BAR'));
    }

    /**
     * @covers ::test
     */
    public function testMaxConstraintCanBeUnsatisfied(): void
    {
        $rule = new UpperCaseRule(0, 3);

        self::assertFalse($rule->test('FOO BAR'));
    }

    /**
     * @covers ::test
     */
    public function testUpperCaseUtf8IsCounted(): void
    {
        $rule = new UpperCaseRule(1, null);

        self::assertTrue($rule->test('Á'));
    }

    /**
     * @covers ::test
     */
    public function testLowerCaseUtf8IsNotCounted(): void
    {
        $rule = new UpperCaseRule(1, null);

        self::assertFalse($rule->test('á'));
    }

    /**
     * @covers ::test
     */
    public function testRuleIsSatisfiedWhenConstraintWeightIsLowerThanTestWeight(): void
    {
        $rule = new UpperCaseRule(0, 3, 1);

        self::assertTrue($rule->test('FOO BAR', 2));
    }

    /**
     * @covers ::validate
     */
    public function testRuleCanBeValidated(): void
    {
        $rule = new UpperCaseRule(1, null);

        self::assertNull($rule->validate('FOO'));
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintCanBeInvalidated(): void
    {
        $rule = new UpperCaseRule(5, null);

        self::assertEquals(
            new ValidationError(
                'The password must contain at least 5 upper case letters.',
                'FOo',
                $rule,
                1
            ),
            $rule->validate('FOo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintCanBeInvalidated(): void
    {
        $rule = new UpperCaseRule(0, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain at most 10 upper case letters.',
                'FOo BAR QWERTY',
                $rule,
                1
            ),
            $rule->validate('FOo BAR QWERTY')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithBothMinAndMaxConstraintCanBeInvalidated(): void
    {
        $rule = new UpperCaseRule(5, 10);

        self::assertEquals(
            new ValidationError(
                'The password must contain between 5 and 10 upper case letters.',
                'FOo',
                $rule,
                1
            ),
            $rule->validate('FOo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMaxConstraintEqualToZeroCanBeInvalidated(): void
    {
        $rule = new UpperCaseRule(0, 0);

        self::assertEquals(
            new ValidationError(
                'The password cannot contain upper case letters.',
                'FOo',
                $rule,
                1
            ),
            $rule->validate('FOo')
        );
    }

    /**
     * @covers ::validate
     */
    public function testRuleWithMinConstraintEqualToMaxConstraintCanBeInvalidated(): void
    {
        $rule = new UpperCaseRule(3, 3);

        self::assertEquals(
            new ValidationError(
                'The password must contain exactly 3 upper case letters.',
                'FOo',
                $rule,
                1
            ),
            $rule->validate('FOo')
        );
    }
}
