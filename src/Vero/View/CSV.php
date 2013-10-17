<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\View;

use Vero\Web\Response;
use Vero\Web\ResponseBody;

/**
 * View for XML response.
 */
class CSV implements ResponseBody
{
    protected $data = array();
    protected $separator;
    protected $fileName;
    
    /**
     * Create view with speciefied column separator.
     * 
     * @param string $sep
     */
    public function __construct($sep = ',')
    {
        $this -> separator = $sep;
    }
    
    /**
     * Set separator
     * 
     * @param array $data
     */
    public function setSeparator($sep)
    {
        $this -> separator = $sep;
    }
    
    /**
     * Set complete data set.
     * 
     * @param array $data
     */
    public function setData($data)
    {
        $this -> data = $data;
    }
    
    /**
     * Add line of data.
     * 
     * @param array $line
     */
    public function addLine($line)
    {
        $this -> data[] = $line;
    }
    
    /**
     * Set file name of attachment.
     * 
     * @param string $name
     */
    public function setFileName($name)
    {
        $this -> fileName = $name;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        $file = $this -> fileName ? $this -> fileName : date('Y-m-d');
        
        $response -> header('Content-Type', 'text/csv; charset=UTF-8');
        $response -> header('Content-disposition', 'attachment; filename='.$file.'.csv');
        $response -> header('Pragma', 'no-cache');
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $output = '';
        
        foreach ($this -> data as $row) {
            $output .= '"'.implode('"'.$this -> separator.'"', str_replace('"', '\"', $row)).'"'."\r\n";
        }
        
        echo $output;
    }
}
