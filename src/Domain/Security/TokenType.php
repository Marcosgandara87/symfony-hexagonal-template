<?php

namespace App\Domain\Security;

enum TokenType: string
{
    case API = 'api';
    case JWT = 'jwt';
}