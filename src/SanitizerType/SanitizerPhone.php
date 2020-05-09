<?php

namespace Feip\SanitizerType;

class SanitizerPhone extends SanitizerType
{
	/**
     *  Type checking
     *  
     *  @param  value   $value
     *  @return bool 
     */
	public function isType($value)
	{
		/*
		 *	Федеральный номер – это 11-значный мобильный номер. 
		 *	Данный номер состоит из кода страны (Россия +7), кода оператора или города 
		 *	(трехзначное число) и самого номера ХХХ-ХХ-ХХ.
		*/
		$value = preg_replace('/[ |\(|\)|\-]/', '', $value);
		if (preg_match('/^(8|\+7|7)[0-9\ \-]{10}/', $value, $matches, PREG_OFFSET_CAPTURE, 0))
		{
			return true;
		}
		return false;
	}
	/**
     *  Сonversion to the required format
     *  
     *  @param  value   $value
     *  @return value 
     */
	public function sanitizeType($value)
	{
		$value = preg_replace('/[ |\(|\)|\-]/', '', $value);
		$value = preg_replace('/^(8|\+7)/', '7', $value);
		return intval($value);
	}
}
