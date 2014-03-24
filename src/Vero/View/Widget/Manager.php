<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View\Widget;

use Vero\DependencyInjection\Container;

/**
 * Widgets are small classes shared between different Views.
 * This class manages widgets in one app namespace.
 */
class Manager
{
    protected $container;
    protected $namespace;
    protected $widgets = array();
    
    /**
     * All widgets have access do DI Container.
     */
    public function __construct(Container $container, $namespace = 'App\Widget')
    {
        $this -> container = $container;
        $this -> namespace = $namespace;
    }
    
    /**
     * Add wiget instance. Optionaly name can be speciefied.
     * 
     * @param \Vero\View\Tal\Widget @widget
     * @param string|null @name
     * return Vero\View\Widget\Manager
     */
    public function add(Widget $widget, $name = null)
    {
        $name = $name ? $name : $this -> getWidgetName($widget);
        $this -> widgets[$name] = $widget;
        return $this;
    }
    
    /**
     * @see get()
     */
    public function __get($name)
    {
        return $this -> get($name);
    }
    
    /**
     * For Twig: always try to call __get.
     */
    public function __isset($name)
    {
        return true;
    }
    
    /**
     * Get widget instance or try to create one.
     * 
     * @param string $name
     * @throws \InvalidArgumentException
     * @return \Vero\View\Widget\Widget
     */
    public function get($name)
    {
        if (!isset($this -> widgets[$name])) {
            $class = $this -> namespace.'\\'.$name;
            
            if (!class_exists($class)) {
                throw new \InvalidArgumentException('Widget '.$name.' is not registered and can not be created.');
            }
            
            $widget = new $class($this -> container);
            $this -> widgets[$name] = $widget;
        }
        
        return $this -> widgets[$name];
    }
    
    /**
     * Get widget name (Class name without namespace).
     * 
     * @param Vero\View\Widget\Widget
     * @return string
     */
    protected function getWidgetName(Widget $widget)
    {
        $t = explode('\\', get_class($widget));
        return array_pop($t);
    }
}
