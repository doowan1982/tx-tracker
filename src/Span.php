<?php
namespace Tesoon\Tracker;
class Span{

    /**
     * @var Span
     */
    private $next;

    /**
     * 本次调用给唯一
     * @var string
     */
    private $spanId;

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
     * 当前调用描述
     * @var string
     */
    private $desc;

    /**
     * 该值一般为所在类名称或者函数名称
     * @var string
     */
    private $targetName = "";

    /**
     * @var int
     */
    private $duration = 0;

    /**
     * @var array
     */
    private $arguments = [];

    /**
     * 最终的代码标签，用于后期的统计
     */
    private $tags = [];


    public static function create(string $desc): Span{
        return new static($desc);
    }

    /**
     * 根据外部调用来创建一个Span，parentId与sendTimestamp为比必传值
     * @param string $desc
     * @param string $parentId
     * @param int $sendTimestamp 外部请求时间
     * @return Span
     */
    public static function persist(string $desc, string $parentId, int $sendTimestamp): Span{
        return (new static($desc))->setParentSpanId($parentId)
                            ->setDuration($sendTimestamp);
    }

    public function __construct(string $desc){
        $this->desc = $desc;
        $this->timestamp = Utils::getIntegerMicroTime();
    }

    /**
     * @return string
     */
    public function getDesc(): string{
        return $this->desc;
    }

    /**
     * @param string $name Only valid
     * @param mixed $value Only valid for primitive types
     * @return Span
     * @see Tesoon\Tracker\SpanTags
     */
    public function tag(string $name, $value): Span{
        if(!Utils::isPrimitiveType($value)){
            return $this;
        }
        if(!isset($this->tags[$name])){
            $this->tags[$name] = [];
        }
        $this->tags[$name][] = $value;
        return $this;
    }

    public function tagForDb($value): Span{
        return $this->tag(SpanTags::SQL, $value);
    }

    public function tagForStatus($value): Span{
        return $this->tag(SpanTags::STATUS_CODE, $value);
    }

    public function tagForUrl($value): Span{
        return $this->tag(SpanTags::URL, $value);
    }

    /**
     * @param object|string $target
     * @return Span
     */
    public function setTargetName($target): Span{
        if(is_object($target)){
            $target = get_class($target);
        }
        $this->targetName = $target;
        return $this;
    }

    /**
     * @return int
     */
    public function getTimestamp(): int{
        return $this->timestamp;
    }

    /**
     * @param string $spanId 
     * @return Span
     */
    public function setSpanId(string $spanId): Span{
        $this->spanId = $spanId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSpanId(){
        if($this->spanId === null){
            $this->spanId = Utils::generateId();
        }
        return $this->spanId;
    }

    /**
     * @param string
     * @return Span
     */
    public function setParentSpanId($parent){
        $this->parent = $parent;
        return $this;
    }

    /**
     * @return string
     */
    public function getParentSpanId(): string{
        return $this->parent;
    }

    /**
     * @param string $key
     * @param mixed $value Only valid for primitive types
     * @return Span
     */
    public function setArgument(string $key, $value){
        if(!Utils::isPrimitiveType($value)){
            return $this;
        }
        $this->arguments[$key] = $value;
        return $this;
    }

    /**
     * @param array $arguments
     * @return Span
     */
    public function setArguments(array $arguments): Span{
        $this->arguments = $arguments;
        return $this;
    }

    /**
     * @return Span
     */
    public function getNext(): Span{
        return $this->next;
    }

    /**
     * 根据上一个Span时间戳来计算上一个
     * @param float $prevTimestamp
     * @return Span
     */
    public function setDuration(int $prevTimestamp): Span{
        $this->duration = $this->timestamp - $prevTimestamp;
        return $this;
    }

    /**
     * @return int
     */
    public function getDuration(): int{
        return $this->duration;
    }

    /**
     * @return array
     */
    public function toArray(): array{
        return [
            'id' => $this->getSpanId(),
            'pid' => $this->getParentSpanId(),
            'mt' => $this->timestamp,
            'desc' => $this->desc,
            'targetName' => $this->targetName,
            'args' => $this->arguments,
            'duration' => $this->duration,
            'tags' => $this->tags,
        ];
    }

}