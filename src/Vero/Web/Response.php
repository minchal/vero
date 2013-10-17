<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\Routing\URL;

/**
 * Response represents all, that action can return to the browser by HTTP:
 *  - header code
 *  - headers
 *  - cookies
 *  - body of response
 * 
 * For response can be registered listeners (of type callback).
 * They will be notified after rendering body and before sending headers of response.
 */
class Response
{
    protected $headerCode;
    protected $headers = [];
    protected $cookies = [];
    protected $body;
    
    protected $cookiePath;
    protected $cookieDomain;
    
    protected $signatureKey;
    
    /**
     * Construct response and start buffering.
     * 
     * @param ResponseBody|string
     */
    public function __construct($body = null)
    {
        // default Content-Type
        $this -> header('Content-Type', 'text/html; charset=UTF-8');
        $this -> body($body);
    }
    
    /**
     * Configure cookie path.
     * 
     * @param string
     * @return self
     */
    public function setCookiePath($path)
    {
        $this -> cookiePath = $path;
        return $this;
    }
    
    /**
     * Configure cookie domain.
     * 
     * @param string
     * @return self
     */
    public function setCookieDomain($domain)
    {
        $this -> cookieDomain = $domain;
        return $this;
    }
    
    /**
     * Send full response and release buffer.
     * 
     * @param array Referance to array with callbacks
     */
    public function send(array &$listeners = [])
    {
        if ($this -> body instanceof ResponseBody) {
            $this -> body -> prepare($this, $this -> getBuffer());
        } else {
            $this -> body .= $this -> getBuffer();
        }
        
        foreach ($listeners as $listener) {
            $listener($this);
        }
        
        $this -> sendHeaders();
        
        if ($this -> body instanceof ResponseBody) {
            $this -> body -> send();
        } else {
            echo $this -> body;
        }
    }
    
    /**
     * Send buffered headers.
     */
    public function sendHeaders()
    {
        if (headers_sent($file, $line)) {
            throw new \LogicException('Headers already send in "'.$file.':'.$line.'"!');
        }
        
        if ($this -> headerCode) {
            //header('HTTP', true, $this -> headerCode);
            header('HTTP/1.0 ' . $this -> headerCode);
        }
        
        foreach ($this -> headers as $k => $v) {
            header($k.($v ? ': '.$v : ''));
        }
        
        foreach ($this -> cookies as $name => $item) {
            if (!$this -> cookieDomain) {
                setcookie($name, $item['value'], $item['expire'], $this -> cookiePath);
            } else {
                setcookie($name, $item['value'], $item['expire'], $this -> cookiePath, $this -> cookieDomain);
            }
        }
    }
    
    /**
     * Set raw response content or object of ResponseBody
     * 
     * @param ResponseBody|string $body
     * @return ResponseBody|string
     */
    public function body($body)
    {
        if (is_object($body) && !$body instanceof ResponseBody && !method_exists($body, '__toString')) {
            throw new \InvalidArgumentException('Body object must be instance of Vero\Web\ResponseBody!');
        }
        
        return $this -> body = $body;
    }
    
    /**
     * Set cookie.
     * Domain and patch will be configured automatically. 
     * 
     * @param string $name
     * @param string $value
     * @param int $expire
     * @return self
     */
    public function cookie($name, $value = null, $expire = 0)
    {
        $this -> cookies[$name] = array(
            'value' => $value,
            'expire' => $expire
        );
        
        return $this;
    }
    
    /**
     * Set header.
     * If you want set few this same headers, set arg $replace to false.
     * 
     * @param string $name Name of header
     * @param mixed $value Value of header
     * @param boolean $replace
     * @return self
     */
    public function header($name, $value = null, $replace = true)
    {
        if ($replace) {
            $this -> headers[$name] = $value;
        } else {
            $this -> headers[$name.($value ? ': '.$value : '')] = '';
        }
        
        return $this;
    }
    
    /**
     * Set Header Code.
     * 
     * @param int $code
     * @return self
     */
    public function headerCode($code)
    {
        $this -> headerCode = $code;
        
        return $this;
    }
    
    /**
     * Set headers for redirect.
     * 
     * @param string|URL $url
     * @param int $code Header Code
     * @return self
     */
    public function redirect($url, $code = 302)
    {
        if ($url instanceof URL) {
            $url -> full();
        }
        
        return $this -> headerCode($code) -> header('Location', (string) $url);
    }
    
    /**
     * Get current http Header Code.
     * 
     * @return int
     */
    public function getHeaderCode()
    {
        return $this -> headerCode;
    }
    
    /**
     * Clear and return buffer content.
     * 
     * @return string
     */
    protected function getBuffer()
    {
        $r = '';
        
        while (ob_get_level()) {
            $r .= ob_get_clean();
        }
        
        return $r;
    }
}
