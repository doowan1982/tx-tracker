<?php
namespace Tesoon\Tests\Classes;

use Tesoon\Tracker\Span;
use Tesoon\Tracker\Tracker;

class Base{
    public static function add($desc, $args){
        Tracker::getInstance()->add(Span::create($desc)->setTargetName(static::class)->setArguments($args));
    }
}

class A extends Base{
    private $params = [];
    public function __construct($params)
    {
        self::add("A class test", [
            'params' => $params
        ]);
        $this->params;
        $this->test();
    }

    public function test(){
        Tracker::getInstance()->add(Span::create('A class method test')->setArguments([
            'params' => "----"
        
        ]));
        return $this->params;
    }
}