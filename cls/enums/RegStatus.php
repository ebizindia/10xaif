<?php
namespace eBizIndia\enums;
enum RegStatus: string {
	case N = 'New';
	case A = 'Approved';
    case D = 'Disapproved';
    
	public function label(): string
    {
        return match($this) {
            static::N => 'New',
            static::A => 'Approved',
            static::D => 'Disapproved',
            
        };
    }
    
}