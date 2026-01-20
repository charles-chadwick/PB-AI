<?php

namespace App\Enums;

use App\Traits\EnumToSelect;

enum AppointmentType: string
{
    use EnumToSelect;

    case Online   = 'Online';
    case InPerson = 'In Person';
    case FollowUp = 'Follow Up';
    case NewPatient = 'New Patient';
}
