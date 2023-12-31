<?php
namespace Tesoon\Tracker;

class Utils{

    /**
     * Generate a random string of length 32
     * @return string
     */
    public static function generateId(): string{
       return bin2hex(openssl_random_pseudo_bytes(16));
    }

    /**
     * Get a microtime
     * @return float
     */
    public static function getMicroTime(): float{
        return microtime(true);
    }

    /**
     * Get a microtime integer representation 
     * @return int
     */
    public static function getIntegerMicroTime(): int{
        return self::getMicroTime() * 1000 * 1000;
    }

    /**
     * @param mixed $value
     * @return bool
     */
    public static function isPrimitiveType($value): bool{
        return is_int($value) || is_float($value) || is_bool($value) || is_string($value);
    }

}