<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View;

use Vero\Web\Response;
use Vero\Web\ResponseBody;

/**
 * Simple json output view.
 */
class Json implements ResponseBody
{
    protected $data;
    protected $encoded;
    
    /**
     * @param mixed $data
     */
    public function __construct($data = null)
    {
        $this -> setData($data);
    }
    
    /**
     * Set data for this view.
     * 
     * @param mixed $data
     */
    public function setData($data)
    {
        $this -> data = $data;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        $response -> header('Content-Type', 'application/json; charset=UTF-8');
        
        $this -> encoded = json_encode($this -> data);
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        echo $this -> encoded;
    }
}
