<?php

namespace App\Enums;

enum TypeNotificationEnum:string  {
    case Question   = 'question';
    case Sale       = 'sale';
    case Message    = 'message';
}
