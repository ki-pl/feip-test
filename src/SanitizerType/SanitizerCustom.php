<?php

namespace Feip\SanitizerType;

class SanitizerCustom extends SanitizerType
{
	/**
     *  Type checking
     *  
     *  @param  value   $value
     *  @return bool 
     */
	public function isType($value)
	{
		return true;
	}
	/**
     *  Сonversion to the required format
     *  
     *  @param  value   $value
     *  @return value 
     */
	public function sanitizeType($value)
	{
		return $value;
	}
}
