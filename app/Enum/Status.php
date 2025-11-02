<?php
namespace App\Enum;

enum status:string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';

    public function label():string{
        
        return match($this){
            self::ACTIVE    =>  'active',
            self::INACTIVE  =>  'inactive',
        };
    }
}