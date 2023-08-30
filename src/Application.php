<?php
namespace Tesoon\Tracker;
abstract class Application{

    /**
     * 应用唯一标识
     * @var string
     */
    private $id;

    /**
     * 应用名称
     * @var string
     */
    private $name;

    /**
     * 本次调用链的唯一标识
     * @var string
     */
    private $traceId;

    /**
     * 应用所在ip地址，该地址可能为内网IP
     * @var string
     */
    private $ip;

    private $timestamp;

    /**
     * 应用名称
     * @var Operator
     */
    private $operator;

    /**
     * 当前应用是否在调用链的头部，如果构造参数指明$traceId，则该值为false
     * @var bool
     */
    private $isHead = true;

    /**
     * @param string $id
     * @param string $name
     * @param Operator $operator
     * @param string $traceId 如果本次调用起源于上个应用，此处将不能为空
     */
    public function __construct($id, $name, Operator $operator = null, $traceId = null){
        $this->id = $id;
        $this->name = $name;
        $this->operator = $operator;
        $this->traceId = $traceId;
        if($this->traceId){
            $this->isHead = false;
        }
        $this->timestamp = Utils::getIntegerMicroTime();
    }

    public function getId(): string{
        return $this->id;
    }

    public function getName(): string{
        return $this->name;
    }

    public function getOperator(): Operator{
        if($this->operator === null){
            $this->operator = $this->createOperator();
        }
        return $this->operator;
    }

    public function setIp($ip){
        $this->ip = $ip;
    }

    public function getIp(): string{
        return $this->ip;
    }

    /**
     * @return string
     */
    public function getTraceId(): string{
        //调用链首部，如果为设置traceId，则自动生成
        if($this->traceId === null){
            $this->traceId = Utils::generateId();
        }
        return $this->traceId;
    }

    /**
     * @param string $traceId
     * @return Span
     */
    public function setTraceId($traceId){
        $this->traceId = $traceId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isHead(): bool{
        return $this->isHead;
    }

    public function getTimestamp(): int{
        return $this->timestamp;
    }

    /**
     * @return DataPackage
     */
    public function getDataPackage(): DataPackage{
        return new DataPackage($this);
    }

    /**
     * 创建一个操作者
     * 如果是一个实际的用户，则直接绑定，否则需要计算客户端的当前请求信息
     * @return Operator
     */
    protected abstract function createOperator(): Operator;


}