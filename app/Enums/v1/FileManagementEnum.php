<?php

namespace App\Enums\v1;

enum FileManagementEnum: int
{
    case NOT_PROCESSED = 0; 
    case SUCCESS_ON_PROCESS = 1; 
    case FAILED_ON_PROCESS = 2; 
    case IN_PROGRESS = 3;
}