<?php


namespace Feip;

use InvalidArgumentException;

class Sanitizer
{
	/**
     *  Data to sanitize
     *  @var json
     */
    protected $data;

    /**
     *  Rules to apply
     *  @var array
     */
    protected $rules;

    /**
     *  Errors
     *  @var array
     */
    protected $errors;

    /**
     *  Types to apply
     *  @var array
     */
    protected $types = array(
    	'int',
    	'float',
    	'string',
    	'phone',
    	'int[]',
    	'float[]',
    	'string[]',
    	'phone[]'
    );

	/**
     *  Create a new sanitizer instance.
     *
     *  @param  json    $data
     *  @param  array   $rules 
     *  @param  array   $customTypes 
     *  @return Sanitizer
     */
    public function __construct(string $data, array $rules,array $customTypes = [])
    {
    	if ($this->isJson($data))
    	{
        	$this->data = json_decode($data,1);
        }else
        {
        	$this->error = 'Invalid JSON format';
        	throw new InvalidArgumentException("Invalid JSON format");
        	
        }
        $this->types = array_merge($this->types,$customTypes);
        $this->rules = $this->parseRules($rules);
        $this->errors = array();
    }

    /**
     *  Start Sanitizer
     *
     *  @return array 
     */

	public function sanitize()
    {
        return $this->sanitizeData($this->data,$this->rules);
    }

	/**
     *  Data processing
     *
     *  @param  array   $data
     *  @param  array   $rules 
     *  @param  string  $path 
     *  @return array 
     */

    public function sanitizeData(array $data,array $rules,string $path = 'root')
    {
    	$sanitizeData=[];	
    	foreach ($data as $field => $fieldValue) 
        {
        	if (is_array($rules[$field]))
        	{
        		$sanitizeData[$field] = $this->sanitizeData($fieldValue,$rules[$field],$path.' > '.$field);
        	}
        	elseif ( is_array($data[$field]) )
        	{
        		$sanitizeData[$field] = $this->sanitizeArray($fieldValue,$rules[$field],$path.' > '.$field);
        	}else
        	{
        		$sanitizeData[$field] = $this->sanitizeValue($fieldValue,$rules[$field],$path.' > '.$field);
        	}
        }
        return $sanitizeData;

    }
	/**
     *  
     *  
     *  @param  array   $data
     *  @param  array   $rule 
     *  @param  string  $path 
     *  @return array 
     */
    private function sanitizeArray(array $data,string $rule,string $path)
    {
        if (!$this->isRuleArray($rule)) throw new InvalidArgumentException("Type mismatch: unexpected array");
        $sanitizeArray = [];
        $SanitizerType = ucfirst(trim($rule,'[]'));
        $SanitizerTypeClass = 'Feip\SanitizerType\Sanitizer'.$SanitizerType;
    	$rule = new $SanitizerTypeClass;
    	
    	foreach ($data as $key => $value)
        {
        	if ($rule->isType($value)) 
				$sanitizeArray[$key] = $rule->sanitizeType($value);
			else
			{
				$this->errors[] = $path.' > '.$value.' : invalid array value type. Must be '.$SanitizerType;
			}
        }
    	return $sanitizeArray;
	}
	/**
     *  
     *  
     *  @param  array   $value
     *  @param  array   $rule 
     *  @param  string  $path 
     *  @return value 
     */
    private function sanitizeValue(string $value, string $rule, string $path)
    {
    	if ($this->isRuleArray($rule)) throw new InvalidArgumentException("Type mismatch: unexpected array");
    	$SanitizerType = ucfirst($rule);
    	$SanitizerTypeClass = 'Feip\SanitizerType\Sanitizer'.ucfirst($rule);
    	$rule = new $SanitizerTypeClass;
    	if ($rule->isType($value)) 
    		return $rule->sanitizeType($value);
    	else
    	{
    		$this->errors[] = $path.' > '.$value.' : unexpected data type. Must be '.$SanitizerType;
    	}
	}
	/**
     *  Get a list of errors
     *  
     *  @return json 
     */
    public function getError()
    {
    	return json_encode($this->errors,JSON_UNESCAPED_UNICODE);
    }
	/**
     *  Validation of processing rules
     *  
     *  @param  array   $rules 
     *  @return array 
     */
    private function parseRules(array $rules)
    {
 		$parsedRules = [];
		if (empty($rules))
        {
        	throw new InvalidArgumentException("Empty list of conversion rules");
        }
        foreach ($rules as $field => $fieldRule) 
        {
            if (is_array($fieldRule))
        	{	
        		$parsedRules[$field] = $this->parseRules($fieldRule); 
        	}
        	else
	        {
		        if ($this->validateType($fieldRule))
		        	$parsedRules[$field] = $fieldRule;
		        else 
		        	{	
		        		throw new InvalidArgumentException("Invalid  conversion rules");
		        	}
	        }
	       	
        }
        return $parsedRules;
    }
	/**
     *  Data is Json? 
     *  
     *  @param  json   $data 
     *  @return bool 
     */
	private function isJson( $data = null ) 
	{
		if( ! empty( $data ) ) {
			$tmp = json_decode( $data );
			return (
					json_last_error() === JSON_ERROR_NONE
					&& ( is_object( $tmp ) || is_array( $tmp ) )
			);
		}
		return false;
	}

	/**
     *  Does the rule contain an array?
     *  
     *  @param  value   $value 
     *  @return bool 
     */
	private function isRuleArray($value)
	{
		if ( stripos($value,'[]') === false)
		return false;
		else return true;
	}

	/**
     *  validateType
     *  
     *  @param  value   $value 
     *  @return bool 
     */
	private function validateType($value)
	{
		return in_array($value,$this->types);
	}
	
}
