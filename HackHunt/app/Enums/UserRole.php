<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case RESEARCHER = 'researcher';

    /**
     * Get numeric representation of roles
     */
    public function label(): string
    {
        return match ($this) {
            self::ADMIN => '3',
            self::CUSTOMER => '2',
            self::RESEARCHER => '1',
        };
    }

    /**
     * Convert number to UserRole enum case
     */
    public static function fromNumber(int $number): self
    {
        return match ($number) {
            3 => self::ADMIN,
            2 => self::CUSTOMER,
            1 => self::RESEARCHER,
            default => throw new \InvalidArgumentException('Invalid role number'),
        };
    }
}
