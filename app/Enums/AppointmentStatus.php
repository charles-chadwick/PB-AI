<?php

namespace App\Enums;

use App\Traits\EnumToSelect;

enum AppointmentStatus: string
{
    use EnumToSelect;

    case Scheduled   = 'Scheduled';
    case Completed   = 'Completed';
    case Cancelled   = 'Cancelled';
    case NoShow      = 'No Show';
    case Rescheduled = 'Rescheduled';
    case Confirmed   = 'Confirmed';
}
