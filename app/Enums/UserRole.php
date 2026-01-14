<?php

namespace App\Enums;

use App\Traits\EnumToSelect;

enum UserRole: string
{
    use EnumToSelect;
    case Admin     = 'Admin';
    case Doctor    = 'Doctor';
    case Nurse     = 'Nurse';
    case FrontDesk = 'Front Desk';
}
