<?php
namespace Tesoon\Tracker;

class Header{

    const TRACE_ID = 'X-TRACE-ID';
    const TRACE_SPAN_ID = 'X-TRACE-SPAN-ID';
    const TRACE_TIMESTAMP = 'X-TRACE-TIMESTAMP';

    private $headers = [];

    /**
     * @return array
     */
    public function get(): array{
        return $this->headers;
    }

    /**
     * @param string $name
     * @param string $value
     * @return Header
     */
    public function setHeader(string $name, string $value): Header{
        $this->headers[$name] = $value; 
        return $this;
    }
    
    /**
     * @param string $traceId
     * @return Header
     */
    public function setTraceId(string $traceId): Header{
        $this->headers[self::TRACE_ID] = $traceId;
        return $this;
    }

    /**
     * @param string $spanId
     * @return Header
     */
    public function setSpanId(string $spanId): Header{
        $this->headers[self::TRACE_SPAN_ID] = $spanId;
        return $this;
    }

    /**
     * @param int $timestamp
     * @return Header
     */
    public function setTimestamp(int $timestamp){
        $this->headers[self::TRACE_TIMESTAMP] = $timestamp;
        return $this;
    }

    /**
     * @param Span $span
     * @return Header
     */
    public function setSpan(Span $span): Header{
        $this->setSpanId($span->getSpanId());
        $this->setTimestamp($span->getTimestamp());
        return $this;
    }

}