<?php
namespace Tesoon\Tracker;

use Tesoon\Tracker\Exception\TraceException;

final class Tracker{

    private static $traker;

    public static function getInstance(){
        if(self::$traker === null){
            self::$traker = new static();
        }
        return self::$traker;
    }

    /**
     * @var Application
     */
    private $application;

    /**
     * @var SpanCollection
     */
    private $collection;

    public function __construct(){
        $this->collection = new SpanCollection();
    }

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
     * 调用链超过该值将触发自动发送数据，0为始终在调用结束发送数据
     * @var int
     */
    private $spanCount = 0;

    /**
     * 设置collection的长度，如果超过该值，将自动保存数据
     * 该方法可在处理过程中进行多次调用
     * @param int $spanCount
     */
    public function setSpanCount(int $spanCount = 0): Tracker{
        $this->spanCount = $spanCount;
        return $this;
    }

    /**
     * @param Span $span
     * @return Tracker
     */
    public function add(Span $span): Tracker{
        $current = $this->collection->current();
        if($this->spanCount > 0 && 
                $this->collection->size() >= $this->spanCount){
            $this->flush();
            if($current != null){
                $span->setParentSpanId($current->getSpanId());
                $span->setDuration($current->getTimestamp());            
            }
        }
        $this->collection->add($span);
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
     * @return SpanCollection
     */
    public function getSpanCollection(): SpanCollection{
        return $this->collection;
    }

    /**
     * 将数据通过sender推送到外部
     * 为保证数据被有效刷新到远端仓库，考虑使用register_shutdown_function
     * @return bool
     * @throws TraceException
     */
    public function flush(): bool{
        if($this->sender === null){
            throw new TraceException("Please indicate DataSender!");
        }
        if($this->collection->size() === 0){
            return true;
        }
        return $this->sender->send($this->application->getDataPackage());
    }

}