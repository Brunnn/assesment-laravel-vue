<?php 
namespace App\Enums;

use App\Traits\EnumUtils;


enum GenericFilterNumberOperationsEnum: string{

    use EnumUtils;
    case EQUALS = 'equals';
    case LESS_THAN  = 'lessThan';
    case GREATER_THAN = 'greaterThan';

}
