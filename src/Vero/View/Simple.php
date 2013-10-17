<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View;

use Vero\Web\Response;
use Vero\Web\ResponseBody;

/**
 * Simple PHP View.
 */
class Simple implements ResponseBody
{
    protected $tpl;
    protected $data;
    
    /**
     * Create view with speciefied template file name.
     * 
     * @param string|null
     */
    public function __construct($tpl = null, array $data = [])
    {
        if ($tpl) {
            $this -> setTemplate($tpl);
        }
        
        $this -> data = $data;
    }
    
    /**
     * Set template file.
     * 
     * @return self
     */
    public function setTemplate($tpl)
    {
        $this -> tpl = $tpl;
        return $this;
    }
    
    /**
     * @see set()
     */
    public function __set($name, $value)
    {
        return $this -> set($name, $value);
    }
    
    /**
     * Add variable to template.
     * 
     * @param string
     * @param mixed
     * @return self
     */
    public function set($name, $value)
    {
        $this -> data[$name] = $value;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        $response -> header('Content-Type', 'text/html; charset=UTF-8');
        
        $this -> set('debug', $buffer);
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        if (!file_exists($this -> tpl)) {
            throw new \RuntimeException(sprintf('Template file %s not found!', $this -> tpl));
        }
        
        extract($this -> data);
        
        require $this -> tpl;
    }
}
