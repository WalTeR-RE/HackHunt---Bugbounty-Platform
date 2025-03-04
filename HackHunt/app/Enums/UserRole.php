<?php

namespace App\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CUSTOMER = 'customer';
    case RESEARCHER = 'researcher';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => '3',
            self::CUSTOMER => '2',
            self::RESEARCHER => '1',
        };
    }
}
