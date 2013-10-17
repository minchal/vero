<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Log;

use Psr\Log\LoggerTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\InvalidArgumentException;

/**
 * Log service compatible with PSR-3.
 * Can use different backends.
 * 
 * @see https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;
    
    protected static $order = [
        'debug' => 0,'info' => 1,'notice' => 2,'warning' => 3,'error' => 4,
        'critical' => 5,'alert' => 6,'emergency' => 7
    ];
    
    protected $level;
    protected $backend;
    
    /**
     * Construct Log service with specified level and backend.
     * 
     * @param string $lvl
     * @param Backend $backend
     */
    public function __construct($level, Backend $backend)
    {
        if (!isset(self::$order[$level])) {
            throw new InvalidArgumentException('Log level "'.$level.'" not supported!');
        }
        
        $this -> level   = $level;
        $this -> backend = $backend;
    }
    
    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = [])
    {
        if (!isset(self::$order[$level])) {
            throw new InvalidArgumentException('Log level "'.$level.'" not supported!');
        }
        
        if ($this -> compare($level, $this->level) >= 0) {
            $this -> backend -> log(
                self::interpolate((string) $message, $context),
                $level,
                isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '127.0.0.1'
            );
        }
    }
    
    /**
     * Interpolate message with context.
     * 
     * @param string
     * @return string
     */
    protected static function interpolate($message, array $context = [])
    {
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }
        
        return strtr($message, $replace);
    }
    
    /**
     * Compare log levels.
     * Return >0 if A is more important, <0 if B is more important, =0 if are equal.
     * 
     * @param string
     * @param string
     * @return int
     */
    protected static function compare($a, $b)
    {
        return self::$order[$a] - self::$order[$b];
    }
}
