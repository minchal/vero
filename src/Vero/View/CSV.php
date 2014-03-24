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
    protected $charset = 'UTF-8';
    
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
     * @return self
     */
    public function setCharset($charset)
    {
        $this -> charset = $charset;
        return $this;
    }
    
    /**
     * Set separator
     * 
     * @param array $data
     * @return self
     */
    public function setSeparator($sep)
    {
        $this -> separator = $sep;
        return $this;
    }
    
    /**
     * Set complete data set.
     * 
     * @param array $data
     * @return self
     */
    public function setData($data)
    {
        $this -> data = $data;
        return $this;
    }
    
    /**
     * Add line of data.
     * 
     * @param array $line
     * @return self
     */
    public function addLine($line)
    {
        $this -> data[] = $line;
        return $this;
    }
    
    /**
     * Set file name of attachment.
     * 
     * @param string $name
     * @return self
     */
    public function setFileName($name)
    {
        $this -> fileName = $name;
        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function prepare(Response $response, $buffer = null)
    {
        $file = $this -> fileName ? $this -> fileName : date('Y-m-d');
        
        $response -> header('Content-Type', 'text/csv; charset=' . $this -> charset);
        $response -> header('Content-disposition', 'attachment; filename='.$file.'.csv');
        $response -> header('Pragma', 'no-cache');
    }
    
    /**
     * {@inheritdoc}
     */
    public function send()
    {
        $output = '';
        $charset = $this -> charset;
        
        foreach ($this -> data as $row) {
            if ($charset != 'UTF-8') {
                $row = array_map(function($i) use ($charset) {
                    return iconv('UTF-8', $charset, $i);
                }, $row);
            }
            
            $output .= '"'.implode('"'.$this -> separator.'"', str_replace('"', '\"', $row)).'"'."\r\n";
        }
        
        echo $output;
    }
}
