<?php 
namespace App\Enums;

use App\Traits\EnumUtils;


enum GenericFilterConditionTypeEnum: string
{
    use EnumUtils;
    case TEXT = 'text';
    case NUMBER = 'number';
    case DATE = 'date';
}
