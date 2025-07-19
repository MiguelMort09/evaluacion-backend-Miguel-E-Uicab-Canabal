<?php

namespace App\Enums;

enum ProductStatus: int
{
    case DRAFT = 1;
    case ACTIVE = 2;
    case WITHOUT_STOCK = 3;
    case DELETED = 4;
    case DISCONTINUED = 5;
}
