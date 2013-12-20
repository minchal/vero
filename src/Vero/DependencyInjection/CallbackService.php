<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection;

/**
 * Lazy Service implementation with callback/anonymous function.
 */
class CallbackService extends LazyService
{
    protected $callback;
    
    /**
     * Create service instance by callback.
     * 
     * DI Container is first and only argument for callback.
     */
    public function __construct(callable $callback)
    {
        $this -> callback = $callback;
    }
    
    /**
     * {@inheritdoc}
     */
    protected function create(Container $container)
    {
        $c = $this -> callback;
        return $c($container);
    }
}
