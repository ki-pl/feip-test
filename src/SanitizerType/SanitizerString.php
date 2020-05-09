<?php

namespace Feip\SanitizerType;

class SanitizerString extends SanitizerType
{
	/**
     *  Type checking
     *  
     *  @param  value   $value
     *  @return bool 
     */
	public function isType($value)
	{
		if (is_string($value) === false ) {
  			return false;//throw new InvalidArgumentException("Variable is not a string");
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
		return $value;
	}
}
