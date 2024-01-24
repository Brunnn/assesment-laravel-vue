<?php 
namespace App\Enums;

use App\Traits\EnumUtils;

enum GenericFilterTextOperationsEnum: string{

    use EnumUtils;
    case CONTAINS = 'contains';
}
