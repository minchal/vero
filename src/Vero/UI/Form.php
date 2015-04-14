<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

use IteratorAggregate;
use ArrayIterator;
use Vero\DependencyInjection\Container;
use Vero\Web\Request;
use Vero\Web\Session\Session;
use Vero\Validate\Validator;
use Vero\Validate\Container as VFC;

/**
 * Class represents instance of form.
 * Form can be secured by CSRF token and instance token (both optional).
 * 
 * Instance keep info about:
 *  - form status: INIT, SENT, ERRORS, FINISHED
 *  - current fields values
 *  - fields errors
 *  - fields validation rules
 * 
 */
class Form implements IteratorAggregate
{
    /**
     * For unique default form name generation.
     * 
     * @var integer
     */
    protected static $instances = 0;
    
    /**
     * Real status is unknown.
     * (Waiting for first use)
     */
    const CREATED = -1;
    
    /**
     * Form wasn't sent.
     */
    const INIT = 0;
    
    /**
     * Form was sent.
     */
    const SENT = 1;
    
    /**
     * Form was sent and no errors was reported during validation.
     */
    const FINISHED = 3;
    
    /**
     * @var Request
     */
    protected $request;
    
    /**
     * @var Session
     */
    protected $session;
    
    /**
     * @var Validator
     */
    protected $validator;
    
    /**
     * @var string|\Vero\Router\URL
     */
    protected $action;
    
    protected $status = self::CREATED;
    
    protected $name   = 'form0';
    protected $method = Request::POST;
    
    protected $autoFinish = true;
    protected $useCSRF  = true;
    protected $useToken = true;
    protected $token;
    
    /**
     * @var array
     */
    protected $value = [];
    
    /**
     * @var array
     */
    protected $errors = [];
    
    /**
     * Create instance of form from raw DI Container.
     * 
     * @return self
     */
    public static function create(Container $container)
    {
        return new static($container->get('request'), $container->get('session'));
    }
    
    /**
     * Create instance of form.
     * Form needs request and optionaly session instances (if tokens are used).
     * 
     * @param \Vero\Web\Request
     * @param \Vero\Web\Session\Session
     */
    public function __construct(Request $request, Session $session = null)
    {
        $this -> request = $request;
        $this -> session = $session;
        
        $this -> action = $request -> url();
        $this -> name   = 'form'.self::$instances++;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if (is_array($this -> value)) {
            return new ArrayIterator($this -> value);
        }
        
        if ($this -> value instanceof \Traversable) {
            return $this -> value;
        }
        
        throw new \LogicException(
            'To use Form as Iterator, set value as array or other iterator (e.g. Validator instance)!'
        );
    }
    
    /**
     * Try to set real status of form.
     * Method is executed only if current status is "created".
     * 
     * If errors with tokens will be detected exception will be thrown.
     * 
     * @throws \Vero\UI\Exception
     * @return self|false
     */
    public function prepare()
    {
        if ($this -> status != self::CREATED) {
            return false;
        }
        
        if (($this -> useToken || $this -> useCSRF) && ! $this -> session instanceof Session) {
            throw new \LogicException('If you want to use tokens, instance of Session must be specified!');
        }
        
        $fun = strtolower($this -> method);
        
        if ($this -> request -> method() == $this -> method &&
            $this -> request -> $fun($this -> name)
        ) {
            $this -> status = self::SENT;
            
            // set default values from request data (can by overwritten by setValue())
            $this -> value = $this -> request -> $fun();
            $this -> token = $this -> request -> $fun($this -> name);
            
            // check tokens
            if ($this -> useToken && $this -> getBag() -> has($this -> token)) {
                throw new Exception('error token', 'global');
            }
            
            if ($this -> useCSRF && $this -> request -> $fun('csrf') != $this -> csrf()) {
                throw new Exception('error csrf', 'global');
            }
            
        } else {
            $this -> status = self::INIT;
            $this -> token  = self::randomToken();
        }
        
        return $this;
    }
    
    /**
     * If true, form will be finished (token add to bag) 
     * when calling isValid() method and it's result is true.
     * 
     * Default: true
     * 
     * @param boolean
     * @return self
     */
    public function setAutoFinish($b)
    {
        $this -> autoFinish = (boolean) $b;
        return $this;
    }
    
    /**
     * Configure using of token.
     * Using token prevents from sending this same form twice.
     * After close of form, token will be saved in session.
     * 
     * @param boolean
     * @return self
     */
    public function useToken($bool = true)
    {
        $this -> checkConfigurationStatus();
        $this -> useToken = $bool;
        return $this;
    }
    
    /**
     * Configure using of CSRF token.
     * Using CSRF token prevents from CSRF attacks.
     * This token is same for all forms in session.
     * 
     * @param boolean
     * @return self
     */
    public function useCSRF($bool = true)
    {
        $this -> checkConfigurationStatus();
        $this -> useCSRF = $bool;
        return $this;
    }
    
    /**
     * Set name of form.
     * 
     * @param string
     * @return self
     */
    public function setName($name)
    {
        $this -> checkConfigurationStatus();
        $this -> name = $name;
        return $this;
    }
    
    /**
     * Set name of form.
     * 
     * @param \Vero\Routing\URL|string
     * @return self
     */
    public function setAction($url)
    {
        $this -> checkConfigurationStatus();
        $this -> action = $url;
        return $this;
    }
    
    /**
     * Set method of form.
     * Available values: POST, GET
     * 
     * @param string
     * @return self
     */
    public function setMethod($method)
    {
        $this -> checkConfigurationStatus();
        
        $method = strtoupper($method);
        
        if ($method != Request::POST && $method != Request::GET) {
            throw new \DomainException('Form method must be one of Request::POST or Request::GET!');
        }
        
        $this -> method = $method;
        return $this;
    }
    
    /**
     * Set form instance Validator.
     * 
     * @param Validator|VFC
     * @param array|null
     * @return self
     */
    public function setValidator($validator, $fields = null)
    {
        if (!$validator instanceof Validator && !$validator instanceof VFC) {
            throw new \InvalidArgumentException(
                'Validator set for Form must be instance of \Vero\Validate\Validator or \Vero\Validate\Container!'
            );
        }
        
        if ($validator instanceof VFC) {
            $validator = Validator::create($this->request, $validator, $fields);
        }
        
        $this -> validator = $validator;
        
        return $this;
    }
    
    /**
     * Get created Validator instance.
     * 
     * @return Validator
     */
    public function getValidator()
    {
        return $this -> validator;
    }
    
    /**
     * Set input values.
     * 
     * @param object|array
     * @return self
     */
    public function setValue($data)
    {
        $this -> value = $data;
        return $this;
    }
    
    /**
     * Set errors of inputs and change form status to "errors"
     * 
     * @param array $errors
     * @return self
     */
    public function setErrors(array $errors)
    {
        $this -> errors = array_merge($this -> errors, $errors);
        return $this;
    }
    
    /**
     * Set errors of inputs and change form status to "errors"
     * 
     * @param array $errors
     * @return self
     */
    public function setError($field, $msg)
    {
        $this -> errors[$field] = $msg;
        return $this;
    }
    
    /**
     * Set status of this form as "finished" and save token in session if needed.
     * 
     * @return self
     */
    public function finish()
    {
        $this -> prepare();
        
        if ($this -> status == self::FINISHED) {
            throw new \RuntimeException('Form can not be finished two times.');
        }
        
        if ($this -> useToken) {
            $this -> getBag() -> set($this -> token, time());
        }
        
        $this -> status = self::FINISHED;
        
        return $this;
    }
    
    /**
     * Check if this form was sent:
     * in request exists variable with name of this form 
     * and method of request equals with form method.
     * 
     * @return boolean
     */
    public function isSent()
    {
        $this -> prepare();
        return $this -> status >= self::SENT;
    }
    
    /**
     * Check if any errors were set.
     * 
     * If instance of Validator is set:
     *  - set validator as values
     *  - set validator errors as form errors
     * 
     * @return boolean
     */
    public function isValidRaw()
    {
        $this -> prepare();
        
        if ($this -> validator) {
            $this
                -> setValue($this -> validator)
                -> setErrors($this -> validator -> errors());
        }
        
        $result = !$this -> errors;
        
        if ($result && $this -> autoFinish) {
            $this -> finish();
        }
        
        return $result;
    }
    
    /**
     * Check if form is send and valid.
     * 
     * @return boolean
     */
    public function isValid()
    {
        return $this -> isSent() && $this -> isValidRaw();
    }
    
    /**
     * Check if this form is finished.
     * 
     * @return boolean
     */
    public function isFinished()
    {
        $this -> prepare();
        return $this -> status == self::FINISHED;
    }
    
    /**
     * Get complete field info as array:
     * [
     *     name => 'example'
     *     value => '10000'
     *     error => 'integer max 100'
     *     validate => [
     *         required => true
     *         rule => integer
     *         options => [max => 100]
     *     ]
     * ]
     * 
     * @param string
     * @return array
     */
    public function getField($name)
    {
        $field = [
            'name'  => $name,
            'value' => $this -> value($name),
            'error' => null,
            'validate' => [
                'rule'     => null,
                'options'  => []
            ],
            'form' => $this->name()
        ];
        
        if (isset($this->errors[$name])) {
            $field['error'] = $this->errors[$name];
        }
        
        if ($this -> validator) {
            $field['validate'] = $this -> validator -> getFieldRule($name);
        }
        
        return $field;
    }
    
    /**
     * Always try to get field, even if it's only name.
     * 
     * @param string
     * @return true
     */
    public function __isset($name)
    {
        return true;
    }
    
    /**
     * @see getField()
     * @param string
     * @return array
     */
    public function __get($name)
    {
        return $this -> getField($name);
    }

    /**
     * Get name of form instance.
     */
    public function name()
    {
        return $this -> name;
    }
    
    /**
     * Get method of form: post or get
     */
    public function method()
    {
        return $this -> method;
    }
    
    /**
     * Get action of form.
     */
    public function action()
    {
        return (string) $this -> action;
    }
    
    /**
     * Get value of form.
     */
    public function value($name = null)
    {
        if ($name) {
            return self::findValue($this -> value, $name);
        }
        
        return $this -> value;
    }
    
    /**
     * Get reported errors for inputs.
     */
    public function errors()
    {
        return $this -> errors;
    }
    
    /**
     * Get error speciefied field
     */
    public function error($field)
    {
        return isset($this -> errors[$field]) ? $this -> errors[$field] : null;
    }
    
    /**
     * Get current status of form.
     */
    public function status()
    {
        $this -> prepare();
        return $this -> status;
    }
    
    /**
     * Get token.
     */
    public function token()
    {
        $this -> prepare();
        return $this -> token;
    }
    
    /**
     * Get CSRF token.
     */
    public function csrf()
    {
        if (!$this -> session -> csrf) {
            $this -> session -> csrf = self::randomToken();
        }
        
        return $this -> session -> csrf;
    }
    
    /**
     * If status of form was set prevend configuration changes.
     * 
     * @return boolean
     */
    protected function checkConfigurationStatus()
    {
        if ($this -> status != self::CREATED) {
            throw new \BadMethodCallException('Form configuration methods can be used only before real use of form!');
        }
        return true;
    }
    
    /**
     * Get configured Session Bag.
     * 
     * @return \Vero\Session\Bag
     */
    protected function getBag()
    {
        return $this -> session -> getBag('tokens', ['max'=>20]);
    }
    
    /**
     * Create random token.
     * 
     * @return string
     */
    public static function randomToken()
    {
        return md5(uniqid(mt_rand(), true));
    }
    
    /**
     * Find variable in array or object.
     * 
     * @param array|object
     * @param string
     * @return mixed
     */
    protected static function findValue($value, $name)
    {
        if (is_array($value) || $value instanceof \ArrayAccess) {
            return isset($value[$name]) ? $value[$name] : null;
        }
        
        if (isset($value -> $name)) {
            return $value -> $name;
        }
        
        if (is_callable([$value, $name])) {
            return $value -> $name();
        }
        
        if (is_callable([$value, 'get'.$name])) {
            $m = 'get'.$name;
            return $value -> $m();
        }
        
        return null;
    }
}
