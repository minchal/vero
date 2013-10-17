<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web\Action;

use Vero\Web\ActionInterface;
use Vero\DependencyInjection\Container;
use Vero\Web\Request;
use Vero\Web\Response;
use Vero\Web\Exception;
use Vero\Routing\URL;
use Vero\Validate\Validator;
use Vero\UI\Form;
use Vero\UI\Question;
use Vero\UI\DoctrineListing;
use Vero\View\Json;

/**
 * Basic web action.
 */
abstract class Basic implements ActionInterface
{
    /**
     * @var Container
     */
    protected $container;
    
    /**
     * @var \Vero\Web\Controller
     */
    protected $controller;
    
    /**
     * @var \Vero\Routing\Router
     */
    protected $router;
    
    /**
     * @var \Vero\I18n\Translator
     */
    protected $i18n;
    
    /**
     * {@inheritdoc}
     */
    public function __construct(Container $container)
    {
        $this -> container  = $container;
        $this -> controller = $container -> get('controller');
        $this -> router     = $container -> get('router');
        $this -> i18n       = $container -> get('i18n');
        $this -> setDomain();
    }
    
    /**
     * Default throw Not Found exception.
     * 
     * {@inheritdoc}
     */
    public function run(Request $request)
    {
        throw new Exception\NotFound();
    }
    
    /**
     * Get instance of service from DI Container.
     * 
     * @see Vero\DependencyInjection\Container.get()
     * @param string $name
     * @param mixed $args
     * @return mixed
     */
    public function get($name, $args = [])
    {
        return $this -> container -> get($name, (array) $args);
    }
    
    /**
     * Get instance of Response.
     */
    public function response($body = null)
    {
        return new Response($body);
    }
    
    /**
     * Get instance of Response with redirection.
     * If no URL is specified, current Request URL is choosen.
     * 
     * @param \Vero\Router\URL|null
     */
    public function redirect($url = null)
    {
        return $this -> response() -> redirect($url===null ? $this->get('request')->url() : $url);
    }
    
    /**
     * Redirect to returnUrl or default URL.
     * 
     * @param \Vero\Router\URL
     * @return self
     */
    public function returnTo($default)
    {
        return $this -> redirect($this -> returnUrl($default));
    }
    
    /**
     * Get instance of default templated view.
     * PHPDoc here for default configuration.
     * 
     * In typical templating service implementation 
     * tpl file name can be guessed from Route ID.
     * 
     * @param string $tpl
     * @param array $data
     * @return \Vero\View\Twig
     */
    public function render()
    {
        return $this -> get('templating', func_get_args());
    }
    
    /**
     * Get Json response object.
     * 
     * @param mixed
     * @return Json
     */
    public function json($data)
    {
        return new Json($data);
    }
    
    /**
     * Get Doctrine Entity Manager.
     * 
     * @return \Vero\Vendor\Doctrine\EntityManager
     */
    public function em()
    {
        return $this -> get('doctrine');
    }
    
    /**
     * Get repositiory from Doctrine Entity Manager.
     * PHPDoc here for default configuration.
     * 
     * @param string
     * @return \Doctrine\ORM\EntityRepository
     */
    public function repository($repo = null)
    {
        return $this -> get('repository', $repo);
    }
    
    /**
     * Persist single entity in EntityManager and flush.
     * 
     * @param object
     * @return self
     */
    public function persist($entity)
    {
        $this -> em()
            -> persist($entity)
            -> flush();
        
        return $this;
    }
    
    /**
     * Remove single entity from EntityManager and flush.
     * 
     * @param object
     * @return self
     */
    public function remove($entity)
    {
        $this -> em()
            -> remove($entity)
            -> flush();
        
        return $this;
    }
    
    /**
     * Create Form instance with current DI Container.
     * 
     * @param \Vero\Validate\Container
     * @param array|null
     * @return Validator
     */
    public function validator($vfc = null, $fields = null)
    {
        return Validator::create($this -> get('request'), $vfc, $fields);
    }
    
    /**
     * Get Validator Field Container from DI Container factory.
     * 
     * @param string|array
     * @return \Vero\Validate\Container
     */
    public function vfc($name = null, array $args = [])
    {
        return $this -> get('vfc', [$name, $args]);
    }
    
    /**
     * Shortcut to call url() method on Router object.
     * 
     * @see \Vero\Routing\Router::url()
     * @return Vero\Routing\URL
     */
    public function url()
    {
        return call_user_func_array([$this -> router, 'url'], func_get_args());
    }
    
    /**
     * Set domain for Translator.
     * If no domain is speciefied, get domain from routing module name.
     * 
     * @param string
     * @return Vero\I18n\Translator
     */
    public function setDomain($domain = null)
    {
        if (!$domain) {
            list($domain) = explode('/', $this -> get('request') -> action);
        }
        
        return $this -> i18n -> setDomain($domain);
    }
    
    /**
     * Shortcut to call get() method on Translate object.
     * 
     * @return string
     */
    public function i18n($id, $params = [], $domain = null)
    {
        return $this -> i18n -> get($id, $params, $domain);
    }
    
    /**
     * Shortcut to call getGlobal() method on Translate object.
     * 
     * @return string
     */
    public function i18ng($id, $params = [])
    {
        return $this -> i18n -> getGlobal($id, $params);
    }
    
    /**
     * Create Form instance with current DI Container.
     * 
     * @param \Vero\Validate\Validator|\Vero\Validate\Container
     * @param array|null
     * @return Form
     */
    public function form($validator = null, $fields = null)
    {
        $form = Form::create($this -> container);
        
        if ($validator) {
            $form -> setValidator($validator, $fields);
        }
        
        return $form;
    }
    
    /**
     * Create Question Form instance with current DI Container.
     * 
     * @return Question
     */
    public function question()
    {
        return Question::create($this -> container);
    }
    
    /**
     * Create typical Listing (DoctrineListing) from current Request.
     * 
     * @param URL|null
     * @return DoctrineListing
     */
    public function listing($url = null)
    {
        $request = $this -> get('request');
        
        if (!$url) {
            $url = $request -> url;
        }
        
        $listing = new DoctrineListing();
        
        $listing
            -> setPage($request -> page)
            -> setOrder($request -> order)
            -> setUrl($url);
        
        return $listing;
    }
    
    /**
     * @return Exception\NotFound
     */
    public function notFound()
    {
        return Exception\NotFound::url($this->get('request')->url());
    }
    
    /**
     * @return Exception\AccessDenied
     */
    public function accessDenied($resource = null)
    {
        return Exception\AccessDenied::resource($resource);
    }
    
    /**
     * @return \Vero\Application\Exception
     */
    public function exception($msg, $section = null, $params = [])
    {
        return new \Vero\Application\Exception($msg, $section, $params);
    }
}
