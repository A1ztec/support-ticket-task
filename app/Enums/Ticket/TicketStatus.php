<?php


namespace App\Enums\Ticket;


enum TicketStatus: string
{
    case OPEN = 'open';
    case IN_PROGRESS = 'in_progress';
    case CLOSED = 'closed';



    public function title(): string
    {
        return match ($this) {
            self::OPEN => __('Open'),
            self::IN_PROGRESS => __('In Progress'),
            self::CLOSED => __('Closed'),
        };
    }




    public static function options(): array
    {
        return collect(self::cases())->mapWithKeys(fn($item) => [$item->value => $item->title()])->toArray();
    }
}
