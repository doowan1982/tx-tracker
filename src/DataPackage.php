<?php
namespace Tesoon\Tracker;
class DataPackage{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var array
     */
    public $invocations = [];

    public function __construct(Application $application){
        $this->application = $application;
    }

    /**
     * @return array
     */
    public function getJson(Invocation $headInvocation){
        $operator = $this->application->getOperator();
        return [
            'aid' => $this->application->getId(),
            'aip' => $this->application->getIp(),
            'uid' => $operator->id,
            'uname' => $operator->name,
            'uip' => $operator->ip,
            'tid' => $this->application->getTraceId(),
            'mt' => $this->application->getTimestamp(),
            'list' => $headInvocation->toArray()
        ];
    }

}