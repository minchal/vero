<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View;

use Vero\Web\Response;
use Vero\Web\ResponseBody;
use Vero\Filesystem\Node;

/**
 * Output file contents to client directly through readfile().
 * 
 * This View can add only two headers:
 *    Content-Type: application/octet-stream
 *    Content-disposition: attachment; filename=...
 * 
 * rest (if needed) add yourself through \Vero\Web\Response API. 
 * 
 * @see \readfile()
 */
class Attachment implements ResponseBody
{
    protected $path;
    protected $name;
    protected $headers;
    protected $contentType = 'application/octet-stream';
    
    /**
     * If no $name is specified, filename will be guessed from real $path.
     * 
     * @param string $path Real path to file
     * @param string|null $name File name visible for client
     * @param boolean $headers Add default headers?
     */
    public function __construct($path, $name = null, $headers = true)
    {
        $this -> path = $path;
        $this -> name = $name ? $name : basename($path);
        $this -> headers = (boolean) $headers;
        
        if ($path instanceof Node) {
            $this -> setContentType($path -> mime());
        }
    }
    
    /**
     * Set response Content-Type header
     * 
     * @param string
     * @return self
     */
    public function setContentType($type)
    {
        $this -> contentType = $type;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        if ($this -> headers) {
            $response -> header('Content-Type', $this -> contentType);
            $response -> header('Content-disposition', 'attachment; filename='.$this->name);
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        readfile($this -> path);
    }
}
