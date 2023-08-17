<?php
namespace Tesoon\Tracker;
class Invocation{

    /**
     * @var Invocation
     */
    private $next;

    /**
     * 本次调用给唯一
     * @var string
     */
    private $invocationId;

    /**
     * 父级追踪ID
     * @var string
     */
    private $parent = '';

    /**
     * @var int
     */
    private $timestamp = 0;

    /**
     * 描述
     * @var string
     */
    private $desc;

    /**
     * @var InvocationArgument
     */
    private $argument;

    /**
     * 自身以及所有直接间接下一个Invocation的总和
     * @var int
     */
    private $generationCount = 1;

    /**
     * 调用链所在类或者方法函数名称
     * @var object|string
     */
    private $target;

    public static function create(string $desc): Invocation{
        return new static($desc);
    }

    /**
     * 根据外部调用来创建一个Invocation
     * @param string $parentId
     * @param string $traceId
     * @return Invocation
     */
    public static function persist(string $desc, string $parentId): Invocation{
        return (new static($desc))->setParentInvocationId($parentId);
    }

    public function __construct(string $desc){
        $this->desc = $desc;
        $this->timestamp = microtime(true);
    }

    /**
     * @param object|string $target
     * @return Invocation
     */
    public function setTarget($target): Invocation{
        if(is_object($target)){
            $this->target = get_class($target);
        }else{
            $this->target = $target;
        }
        return $this;
    }

    public function getTarget(){
        return $this->target;
    }

    /**
     * @return int
     */
    public function getTimestamp(){
        return $this->timestamp;
    }

    public function setInvocationId(string $invocationId){
        $this->invocationId = $invocationId;
    }

    /**
     * @return string
     */
    public function getInvocationId(){
        if($this->invocationId === null){
            $this->invocationId = $this->generateInvocationId();
        }
        return $this->invocationId;
    }


    /**
     * @param string
     * @return Invocation
     */
    public function setParentInvocationId($parent){
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentInvocationId(): string{
        return $this->parent;
    }

    /**
     * @param array $arguments
     * @return Invocation
     */
    public function setArguments(array $arguments){
        $this->setInvocationArgument(InvocationArgument::create()->setArguments($arguments));
        return $this;
    }

    /**
     * @param InvocationArgument $argument
     * @return Invocation
     */
    public function setInvocationArgument(InvocationArgument $argument){
        $this->argument = $argument;
        return $this;
    }

    /**
     * @return InvocationArgument
     */
    public function getInvocationArgument(): InvocationArgument{
        if($this->argument === null){
            $this->argument = InvocationArgument::create();
        }
        return $this->argument;
    }

    /**
     * @param Invocation $invocation
     */
    public function setNext(Invocation $invocation){
        if(Tracker::getInstance()->getApplication()->getTraceId() && !$this->parent){
            throw new TraceException("当TraceId不为空时，需指定ParentId");
        }
        $this->generationCount++;
        if($this->next === null){
            $invocation->setParentInvocationId($this->getInvocationId());
            $this->next = $invocation;
            return;
        }
        $this->next->setNext($invocation);
    }

    /**
     * @return int
     */
    public function getGenerationCount(){
        return $this->generationCount;
    }

    /**
     * @return Invocation
     */
    public function getNext(): Invocation{
        return $this->next;
    }

    public function getLast(): Invocation{
        $last = $this;
        if($this->next != null){
            $last = $this->next->getLast();
        }
        return $last;
    }

    /**
     * @return array
     */
    public function toArray(): array{
        $array[] = [
            'args' => $this->getInvocationArgument()->toArray(),
            'id' => $this->getInvocationId(),
            'pid' => $this->getParentInvocationId(),
            'mt' => $this->timestamp,
            'desc' => $this->desc,
            'target' => $this->target,
        ];
        if($this->next != null){
            $array = array_merge($array, $this->next->toArray());
        }
        return $array;
    }

    /**
     * @return string
     */
    protected function generateInvocationId(): string{
        $application = Tracker::getInstance()->getApplication();
        return md5($this->parent.uniqid().$application->getTraceId().$application->getId());
    }

}