<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Routing;

/**
 * Action Part for Pattern Routes.
 * 
 * {@inheritdoc}
 */
class PatternActionPart implements ActionPart
{
    /**
     * @var string
     */
    protected $action;
    
    /**
     * @var array
     */
    protected $params = [];
    
    /**
     * Set URL fragment e.g:
     * 
     *   /news/{id}/{slug}
     * 
     * and params to replace:
     *  id => 10
     *  slug => Vero-is-cool
     * 
     * @param string
     */
    public function __construct($action, array $params = [])
    {
        $this -> action = $action;
        $this -> params = $params;
    }
    
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        $action = $this -> action;
        
        foreach ($this->params as $param => $value) {
            $action = str_replace('{'.$param.'}', $value, $action);
        }
        
        return rtrim($action, '/.');
    }
    
    /**
     * {@inheritdoc}
     */
    public function replace($from, $to)
    {
        $this -> params[$from] = $to;
    }
}
