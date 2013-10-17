<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Log\Backend;

use Vero\Log\Backend;

/**
 * Simple TXT file Backend for Log service
 * 
 * Backend keeps files not too big.
 * Big files are being archived.
 */
class File implements Backend
{
    const K32  = 32768;
    const K64  = 65536;
    const K128 = 131072;
    
    protected $dir;
    protected $maxSize;
    
    /**
     * Construct backend.
     * If specified directory not exists, backend will try to create it.
     * 
     * @param string
     * @param int
     */
    public function __construct($dir, $maxSize = self::K128)
    {
        $this -> dir     = $dir;
        $this -> maxSize = $maxSize;
        
        \Vero\Filesystem\Directory::ensure($dir);
    }
    
    /**
     * {@inheritdoc}
     */
    public function log($message, $level, $ip)
    {
        $str2save = date('Y-m-d H:i:s')." [$ip; $level]: \n $message";
        
        $file = $this->dir . 'current.log';
        
        if (file_exists($file) && filesize($file) > $this -> maxSize) {
            $new = $this->dir . date('Y_m_d-H_i_s').'.log';
            rename($file, $new);
            
            // create empty current.log file
            touch($file);
            chmod($file, 0666);
        }
        
        file_put_contents($file, $str2save."\n", FILE_APPEND | LOCK_EX);
    }
}
