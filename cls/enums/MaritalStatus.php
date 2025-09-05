<?php
namespace eBizIndia\enums;
enum MaritalStatus: string {
	case S = 'S';
	case M = 'M';
    case D = 'D';
    case W = 'W';
    case WR = 'WR';

	public function label(): string
    {
        return match($this) {
            static::S => 'Single',
            static::M => 'Married',
            static::D => 'Divorced',
            static::W => 'Widow',
            static::WR => 'Widower'
        };
    }
    
}