<?php

namespace Feip\SanitizerType;

abstract class SanitizerType
{
   	/**
     *  Type checking
     *  
     *  @param  value   $value
     *  @return bool 
     */
    abstract public function isType($value);
   	/**
     *  Сonversion to the required format
     *  
     *  @param  value   $value
     *  @return value 
     */
    abstract public function sanitizeType($value);
}
