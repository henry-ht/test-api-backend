<?php

namespace App\Enums;

enum SaleStateEnum:string {
    case Negotiation    = 'Negotiation';
    case Follow_up      = 'Follow_up';
    case Pending        = 'Pending';
    case InProgress     = 'In_progress';
    case OnHold         = 'On_hold';
    case ClosedLost     = 'Closed_lost';
    case ClosedWon      = 'Closed_won';
    case Cancelled      = 'Cancelled';
    case Archived       = 'Archived';
}