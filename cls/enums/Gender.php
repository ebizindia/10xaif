<?php
namespace eBizIndia\enums;
enum Gender: string {
	case M = 'M';
	case F = 'F';

	public function label(): string
    {
        return match($this) {
            static::M => 'Male',
            static::F => 'Female'
        };
    }
    
}