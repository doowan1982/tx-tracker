<?php
namespace Tesoon\Tracker;

class Header{

    const TRACE_ID = 'X-TRACE-ID';
    const TRACE_SPAN_ID = 'X-TRACE-SPAN-ID';
    const TRACE_TIMESTAMP = 'X-TRACE-TIMESTAMP';
    /**
     * @string
     */
    private $traceId;

    /**
     * @string
     */
    private $spanId;

    /**
     * @int
     */
    private $timestamp;

    /**
     * @return array
     */
    public function get(): array{
        return [
            self::TRACE_ID => $this->getTraceId(),
            self::TRACE_SPAN_ID => $this->getSpanId(),
            self::TRACE_TIMESTAMP => $this->getTimestamp(),
        ];
    }
    
    public function setTraceId(string $traceId): Header{
        $this->traceId = $traceId;
        return $this;
    }

    public function getTraceId(): string{
        return $this->traceId;
    }

    public function setSpanId(string $spanId): Header{
        $this->spanId = $spanId;
        return $this;
    }

    public function setTimestamp(int $timestamp){
        $this->timestamp = $timestamp;
        return $this;
    }

    public function setSpan(Span $span): Header{
        $this->spanId = $span->getSpanId();
        $this->timestamp = $span->getTimestamp();
        return $this;
    }

    public function getSpanId(): string{
        return $this->spanId;
    }

    public function getTimestamp(): int{
        return $this->timestamp;
    }

}