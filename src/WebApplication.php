<?php
namespace Tesoon\Tracker;
class WebApplication extends Application{

    public function __construct($id, $name, Operator $operator = null, string $traceId = null){
        parent::__construct($id, $name, $operator, $traceId);
        $this->setIp(gethostbyname(gethostname()));
    }

    /**
     * @inheritdoc
     */
    protected function createOperator(): Operator{
        //此处用于创建框架内部的用户
        $operator = new Operator();
        $operator->id = getmyuid();
        $operator->name = $this->getIp();
        return $operator;
    }

}