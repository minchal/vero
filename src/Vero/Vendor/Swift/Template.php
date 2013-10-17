<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Swift;

use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

/**
 * Abstract mail template formatted with Twig library and sent by Swift.
 */
abstract class Template
{
    const FORMAT_TEXT  = 'text';
    const FORMAT_HTML  = 'html';
    const FORMAT_NL2BR = 'html+nl2br';
    
    protected $from;
    protected $to;
    
    protected $subject;
    protected $body;
    protected $format;
    
    protected $data = [];
    
    /** @var \Swift_Mailer */
    protected $mailer;
    
    /** @var \Twig_Environment */
    protected $twig;
    
    /**
     * Create mail template with Swiftmailer adn Twig instance.
     * 
     * @param Swift_Mailer $mailer
     * @param Twig_Environment $twig
     */
    public function __construct(Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this -> mailer = $mailer;
        $this -> twig = $twig;
    }
    
    /**
     * Set template name and prepare content.
     * 
     * This method implementation should set subject, body and format fields.
     * 
     * @param string $tpl
     */
    abstract public function setTemplate($tpl);
    
    /**
     * Set message subject.
     * 
     * @param string
     * @return self
     */
    public function setSubject($subject)
    {
        $this -> subject = $subject;
        return $this;
    }
    
    /**
     * Set message subject.
     * 
     * @param string
     * @return self
     */
    public function setBody($body)
    {
        $this -> body = $body;
        return $this;
    }
    
    /**
     * Set recipients.
     * 
     * @param string|array $to
     * @return self
     */
    public function setTo($to)
    {
        $this -> to = $to;
        return $this;
    }
    
    /**
     * Set sender address.
     * 
     * @param string|array $from
     * @return self
     */
    public function setFrom($from)
    {
        $this -> from = $from;
        return $this;
    }
    
    /**
     * Set data to interpolate with message.
     * 
     * @param mixed $data
     * @return self
     */
    public function setData($data)
    {
        $this -> data = $data;
        return $this;
    }
    
    /**
     * Send message.
     * 
     * @return boolean
     */
    public function send()
    {
        $mime = 'text/plain';
        $body = $this -> body;
        
        if ($this -> format == self::FORMAT_HTML || $this -> format == self::FORMAT_NL2BR) {
            $mime = 'text/html';
            
            if ($this -> format == self::FORMAT_NL2BR) {
                $body = nl2br($body);
            }
        }
        
        $message = Swift_Message::newInstance()
            ->setFrom($this -> from)
            ->setTo($this -> to)
            ->setSubject($this -> format($this -> subject, $this -> data))
            ->setBody($this -> format($body, $this -> data), $mime);
        
        return $this -> mailer -> send($message);
    }
    
    /**
     * Interpolate string with data.
     * 
     * @param string $string
     * @param mixed $data
     * @return string
     */
    protected function format($string, $data)
    {
        return $this -> twig -> render($string, $data);
    }
}
