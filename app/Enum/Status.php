<?php
namespace App\Enum;

enum status:string
{
    case ACTIVE = 1;
    case INACTIVE = 0;

    public function label():string{

        return match($this){
            self::ACTIVE    =>  1,
            self::INACTIVE  =>  0,
        };
    }
}
