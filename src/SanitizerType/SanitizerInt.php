<?php

namespace Feip\SanitizerType;

class SanitizerInt extends SanitizerType
{
	/**
     *  Type checking
     *  
     *  @param  value   $value
     *  @return bool 
     */
	public function isType($value)
	{
		if ( filter_var($value, FILTER_VALIDATE_INT) === false ) {
  			return false;
		}
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
		return intval($value);
	}
}
