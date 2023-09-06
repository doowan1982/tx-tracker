<?php
namespace Tesoon\Tracker;
class DataPackage{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var array|null
     */
    private $data;

    public function __construct(Application $application){
        $this->application = $application;
    }

    /**
     * 数据发送后进行重置
     * @see DataSender
     */
    public function reset(){
        $this->data = null;
    }

    /**
     * @return array
     */
    public function toArray(): array{
        if($this->data != null){
            return $this->data;
        }
        $operator = $this->application->getOperator();
        $spans = Tracker::getInstance()->getSpanCollection()->all();
        $current = null;
        foreach($spans as $key=>$span){
            if($current === null){
                $current = $span;
            }else{
                $span->setParentSpanId($current->getSpanId());
                $span->setDuration($current->getTimestamp());
                $current = $span;       
            }
            $spans[$key] = $span->toArray();
        }
        
        return $this->data = [
            'aid' => $this->application->getId(),
            'aip' => $this->application->getIp(),
            'uid' => $operator->id,
            'uname' => $operator->name,
            'uip' => $operator->ip,
            'tid' => $this->application->getTraceId(),
            'mt' => $this->application->getTimestamp(),
            'list' => $spans
        ];
    }

}