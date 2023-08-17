<?php
namespace Tesoon\Tracker;
class CliApplication extends Application{

    public function __construct($id, $name, Operator $operator = null, string $traceId = null){
        parent::__construct($id, $name, $operator, $traceId);
        $this->setIp(gethostbyname(gethostname()));
    }

    protected function createOperator(): Operator{
        $operator = new Operator();
        $operator->id = getmyuid();
        $operator->name = (string)get_current_user();
        $operator->ip = $this->getIp();
        return $operator;
    }

}