<?php
namespace Tesoon\Tracker;

class Header{

    const TRACE_ID = 'TRACE-ID';
    const TRACE_SPAN_ID = 'TRACE-SPAN-ID';
    const TRACE_TIMESTAMP = 'TRACE-TIMESTAMP';
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