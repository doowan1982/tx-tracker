<?php
namespace Tesoon\Tracker;
final class Tracker{

    private static $traker;

    public static function getInstance(){
        if(self::$traker === null){
            self::$traker = new static();
        }
        return self::$traker;
    }

    /**
     * @var callable
     */
    private $exceptionHandler = null;

    /**
     * @var callable
     */
    private $shutdownHandler = null;

    public function setExceptionHandler($exceptionHandler){
        $this->exceptionHandler = $exceptionHandler;
        set_exception_handler($this->exceptionHandler);
    }

    public function setShutdownHandler($shutdownHandler){
        $this->shutdownHandler = $shutdownHandler;
        register_shutdown_function($this->shutdownHandler);
    }

    /**
     * @var Application
     */
    private $application;

    /**
     * @param Application $application
     * @return Tracker
     */
    public function setApplication(Application $application): Tracker{
        $this->application = $application;
        return $this;
    }

    /**
     * @return Application
     * @throws TraceException
     */
    public function getApplication(): Application{
        if($this->application === null){
            throw new TraceException('Application instance is null!');
        }
        return $this->application;
    }

    /**
     * 该值在invocationCount启用时用于记录headInvocation中最后一个Invocation
     * @var Invocation
     */
    private $segementInvocationId;

    /**
     * 调用链超过该值将触发自动发送数据，0为始终在调用结束发送数据
     * @var int
     */
    private $invocationCount = 0;

    /**
     * 设置从$this->headInvocation到子代保留与内存中的数量，如果超过该值，将自动保存数据
     * 该方法可在处理过程中进行多次调用
     * @param int $invocationCount
     */
    public function setInvocationCount(int $invocationCount = 0): Tracker{
        $this->invocationCount = $invocationCount;
        return $this;
    }

    /**
     * @var Invocation
     */
    private $headInvocation;

    /**
     * 追加invocation到headInvocation的调用链队尾
     * @param Invocation $invocation
     * @return Tracker
     */
    public function add(Invocation $invocation): Tracker{
        if(!$this->exceptionHandler){
            $this->setExceptionHandler([$this, 'registerExceptionHandler']);
        }
        if(!$this->shutdownHandler){
            $this->setShutdownHandler([$this, 'registerShutdown']);
        }
        
        if($this->headInvocation === null){
            if($this->segementInvocationId != null){
                $invocation->setParentInvocationId($this->segementInvocationId);
            }
            $this->headInvocation = $invocation;
        }else{
            $this->headInvocation->setNext($invocation);
        }
        
        if($this->invocationCount > 0 && 
                $this->headInvocation->getGenerationCount() >= $this->invocationCount && 
                    $this->save()){
            $this->segementInvocationId = $this->headInvocation->getLast()->getInvocationId();
            $this->headInvocation = null; //重置头信息
        }
        return $this;
    }

    /**
     * @var DataSender
     */
    private $sender;

    /**
     * @var DataSender
     * @return Tracker
     */
    public function setDataSender(DataSender $dataSender){
        $this->sender = $dataSender;
        return $this;
    }

    /**
     * 保存数据
     * @return bool
     */
    public function save(): bool{
        if(!$this->headInvocation){
            return true;
        }
        if($this->sender === null){
            $this->setDataSender(new DataSender());
        }
        return $this->sender->send($this->headInvocation);
    }

    public function registerExceptionHandler($exception){
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $message = "发生异常：{$message}[{$file}:{$line}]";
        $this->add(Invocation::create($message));
        restore_exception_handler();
    }

    public function registerShutdown(){
        $this->save();
    }

}