<?php
require_once('../vendor/autoload.php');
use Feip\Sanitizer;

$data = [
        'foo'    => '123',
        'bar'    => 'qwe',
        'bas'    => '8 (950) 288-56-23',
        'sab'	 => '.42',
        '123'    =>	[
        				'foo'    =>  ['456aaa'],
        				'bar'    =>  ['qwe',111],
        				'bas'    =>  ['4-426-05-57','8950288-56-23'],
        				'sab'	 =>  [
				        				'foo'    =>  456,
				        				'bar'    =>  qwe,
				        				'bas'    =>  8950288-56-23,
				        				'sab'	 =>  '123.124',
				        			 ],
        			],
    ];
$data = json_encode($data);
$rules = [
        'foo'    => 'custom',
        'bar'    => 'string',
        'bas'    => 'phone',
        'sab'	 =>	'float',
        '123'    =>	[
				        'foo'    => 'custom[]',
				        'bar'    => 'string[]',
				        'bas'    => 'phone[]',
				        'sab'	 =>	[
				        				'foo'    =>  int,
				        				'bar'    =>  string,
				        				'bas'    =>  phone,
				        				'sab'	 =>  float,
				        			],
        			],

    ];
    
    try {
    $Sanitizer = new Sanitizer($data,$rules,array('custom','custom[]'));
    
    print_r('<pre>');
    print_r($Sanitizer->sanitize());
    print_r(json_decode($Sanitizer->getError(),1));
    print_r('</pre>');
    
}
catch (InvalidArgumentException $e) { 
    echo $e->getMessage();
}
?>