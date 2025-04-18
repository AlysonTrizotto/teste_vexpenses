<?php

namespace App\Enums\v1;

enum TokenAbility : string
{ 
    case ISSUE_ACCESS_TOKEN = 'issue-access-token';
    case ACCESS_API = 'access-api';
}
