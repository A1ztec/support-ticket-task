<?php


namespace App\Enums\User;


enum UserRole: string
{
    case USER = 'user';
    case ADMIN = 'admin';




    public function title(): string
    {
        return match ($this) {
            self::USER => __('User'),
            self::ADMIN => __('Admin'),
        };
    }


    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($item) => [$item->value => $item->title()])->toArray();
    }
}
