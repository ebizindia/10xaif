<?php
namespace eBizIndia\enums;
enum Role: string {
	case REGULAR = 'REGULAR';
	case ADMIN = 'ADMIN';

	public function label(): string
    {
        return match($this) {
            static::REGULAR => 'Regular',
            static::ADMIN => 'Admin'
        };
    }
    
}