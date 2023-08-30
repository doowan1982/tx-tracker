<?php
namespace Tesoon\Tracker;

final class SpanCollection{

    /**
     * @var array
     */
    private $spans = [];

    /**
     * @var Span
     */
    private $currentSpan;

    /**
     * @param Span $span
     */
    public function add(Span $span){
        $this->spans[$span->getSpanId()] = $this->currentSpan = $span;
    }

    /**
     * Remove the specified span
     * @param Span $span
     */
    public function remove(Span $span){
        unset($this->spans[$span->getSpanId()]);
    }

    /**
     * Get all Span object and reset `spans`,`currentSpan` to default value
     * @return array
     */
    public function all(): array{
        $spans = array_values($this->spans);
        $this->spans = [];
        $this->currentSpan = null;
        return $spans;
    }

    /**
     * @return Span
     */
    public function last(): ?Span{
        $size = $this->size();
        if($size === 0){
            return null;
        }
        return $this->spans[$size-1];
    }

    /**
     * @return Span
     */
    public function current(): ?Span{
        return $this->currentSpan;
    }

    /**
     * @return int
     */
    public function size(): int{
        return count($this->spans);
    }

}