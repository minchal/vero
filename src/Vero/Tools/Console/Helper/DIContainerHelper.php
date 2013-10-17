<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Tools\Console\Helper;

use Vero\DependencyInjection\Container;
use Symfony\Component\Console\Helper\Helper;

/**
 * Helper holds Vero Dependency Injection Container instance for console commands.
 */
class DIContainerHelper extends Helper
{
    /**
     * @var Container
     */
    protected $di;

    /**
     * Constructor
     */
    public function __construct(Container $di)
    {
        $this -> di = $di;
    }

    /**
     * Retrieves DI Container
     *
     * @return Container
     */
    public function getContainer()
    {
        return $this -> di;
    }

    /**
     * @see Helper
     */
    public function getName()
    {
        return 'DIContainer';
    }
}
