<?php
namespace Tesoon\Tracker;
/**
 * 调用参数类
 * 此处记录应当记录关键业务信息，以便问题排查
 * 同时需保证敏感信息不应当记录于此
 */
class InvocationArgument{

    private $data = [];

    public static function create(): InvocationArgument{
        return new static();
    }

    /**
     * 设置参数，
     * @param array $arguments
     * @return InvocationArgument
     */
    public function setArguments(array $arguments): InvocationArgument{
        foreach($arguments as $name => $value){
            if($this->isPrimitiveType($value) || is_array($value)){
                $this->$name = $value;
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function toArray(){
        return $this->data;
    }

    public function __set($name, $value){
        $this->data[$name] = $value;
    }

    public function __get($name){
        return $this->data[$name] ?? '';
    }

    private function isPrimitiveType($value){
        return is_int($value) || is_float($value) || is_bool($value) || is_string($value);
    } 

}