<?php

use PHPUnit\Framework\TestCase;
use Feip\Sanitizer;

class SanitizerTest extends TestCase
{
    /**
     * @expectedException InvalidArgumentException
     */
    public function testisJsonException()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = 'foo/bar';
        $data = json_encode($data);
        $rules = array('');
        $data = new Sanitizer($data,$rules);
    }

    public function testEmptyRulesException()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'foo' => '  123   ',
        ];
        $data = json_encode($data);
        $rules = [  ];

        $data = new Sanitizer($data,$rules);
    }

    public function testOutsideListRulesException()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'foo' => '  123   ',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'notnumber',
        ];

        $data = new Sanitizer($data,$rules);
    }
    

    public function testErrorIntField()
    {
        $data = [
            'foo' => '123a',
            'bar' => '123.0',
            'baz' => 123.1,
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'int',
            'bar' => 'int',
            'baz' => 'int',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $Sanitizer->sanitize();
        $error = json_decode($Sanitizer->getError(),1);
        $this->assertSame('root > foo > 123a : unexpected data type. Must be Int', $error[0]);
        $this->assertSame('root > bar > 123.0 : unexpected data type. Must be Int', $error[1]);
        $this->assertSame('root > baz > 123.1 : unexpected data type. Must be Int', $error[2]);
    }

    public function testIntField()
    {
        $data = [
            'foo' => '123',
            'bar' => 123,
            'baz' => 123.0,
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'int',
            'bar' => 'int',
            'baz' => 'int',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        
        $this->assertSame(123, $data['foo']);
        $this->assertSame(123, $data['bar']);
        $this->assertSame(123, $data['baz']);
    }

    public function testErrorFloatField()
    {
        $data = [
            'foo' => '123.1a',
            'bar' => '123,0',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'float',
            'bar' => 'float',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        $error = json_decode($Sanitizer->getError(),1);
        $this->assertSame('root > foo > 123.1a : unexpected data type. Must be Float', $error[0]);
        $this->assertSame('root > bar > 123,0 : unexpected data type. Must be Float', $error[1]);
    }

    public function testFloatField()
    {
        $data = [
            'foo'    => '123.1',
            'bar'    => 123.1,
            'baz'    => 1e7,
            'zap'    => .42,
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'float',
            'bar' => 'float',
            'baz' => 'float',
            'zap' => 'float',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        
        $this->assertSame(123.1, $data['foo']);
        $this->assertSame(123.1, $data['bar']);
        $this->assertSame(10000000, $data['baz']);
        $this->assertSame(0.42, $data['zap']);
    }
 
    public function testErrorPhoneField()
    {
        $data = [
            'foo' => '260557',
            'bar' => 260557,
            'baz' => '+1 (950) 288-56-23',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'phone',
            'bar' => 'phone',
            'baz' => 'phone',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        $error = json_decode($Sanitizer->getError(),1);
        $this->assertSame('root > foo > 260557 : unexpected data type. Must be Phone', $error[0]);
        $this->assertSame('root > bar > 260557 : unexpected data type. Must be Phone', $error[1]);
        $this->assertSame('root > baz > +1 (950) 288-56-23 : unexpected data type. Must be Phone', $error[2]);
    }

    public function testPhoneField()
    {
        $data = [
            'foo'    => '+7 (950) 288-56-23',
            'bar'    => 79502885623,
            'baz'    => '8950288-56-23',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'phone',
            'bar' => 'phone',
            'baz' => 'phone',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        
        $this->assertSame(79502885623, $data['foo']);
        $this->assertSame(79502885623, $data['bar']);
        $this->assertSame(79502885623, $data['baz']);
    }

    public function testCustomRule()
    {
        $data = [
            'foo'    => '123qaz',
            'bar'    => ['123qaz'],
            'baz'    => '123',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'custom',
            'bar' => 'custom[]',
            'baz' => 'int',
        ];
        $customRule = array('custom','custom[]');

        $Sanitizer = new Sanitizer($data,$rules,$customRule);
        $data = json_decode($Sanitizer->sanitize(),1);
        
        $this->assertSame('123qaz', $data['foo']);
        $this->assertSame(array('123qaz'), $data['bar']);
        $this->assertSame(123, $data['baz']);
    }

    public function testValueNotArray()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'foo'    => '123qaz',
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'int[]',
        ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
    }
    public function testArrayNotValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $data = [
            'foo'    => ['123qaz'],
        ];
        $data = json_encode($data);
        $rules = [
            'foo' => 'int',
        ];
        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        
    }

    public function testArrayInArray()
    {
        $data = [
        'foo'    => [
                        'foo'    =>  [
                                        'foo'    =>  '456',
                                        'bar'    =>  'qwe',
                                        'bas'    =>  '8950288-56-23',
                                        'sab'    =>  '123.124',
                                     ],
                    ],
        ];
        $data = json_encode($data);
        
        $rules = [
        'foo'    => [
                        'foo'    => [
                                        'foo'    =>  'int',
                                        'bar'    =>  'string',
                                        'bas'    =>  'phone',
                                        'sab'    =>  'float',
                                    ],
                    ],

    ];

        $Sanitizer = new Sanitizer($data,$rules);
        $data = json_decode($Sanitizer->sanitize(),1);
        $this->assertSame(true, is_array($data['foo']['foo']));
        
    }


}
