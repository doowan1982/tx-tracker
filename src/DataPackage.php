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
    public $spans = [];

    public function __construct(Application $application){
        $this->application = $application;
    }

    /**
     * 设置打包发送的span
     * @param array $spans
     */
    public function setSpans(array $spans){
        $this->spans = $spans;
    }

    /**
     * @return array
     */
    public function toArray(): array{
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
        
        return [
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