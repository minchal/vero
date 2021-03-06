<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

use Vero\Application as App;

/**
 * Web Controller runs action basing on request query.
 */
class Controller extends App\Controller
{
    protected $exceptionHandler = '\Vero\Web\ExceptionHandler';
    protected $listeners = [];
    
    /**
     * Set name of action class, that will be runned when \Vero\App\Exception occured.
     * Class must be of type \Vero\Web\ActionInterface
     * 
     * @see ActionInterface
     * @param string
     * @return self
     */
    public function setExceptionHandler($actionClass)
    {
        $this -> exceptionHandler = $actionClass;
        return $this;
    }
    
    /**
     * Add listener, that will be called after Request rendering, but before headers send.
     * Callable will get one argument: \Vero\Web\Response instance.
     * 
     * @see \Vero\Web\Response::send()
     * @return self
     */
    public function addSendListener(callable $listener)
    {
        $this -> listeners[] = $listener;
        return $this;
    }
    
    /**
     * Try to run action basing on query URI.
     * Catch Vero\App\Exception to display nice messages by exceptionHandler Action.
     */
    public function run()
    {
        $request  = $this -> container -> get('request');
        
        ob_start();
        
        try {
            if (!$class = $this -> findAction($request)) {
                throw Exception\NotFound::url($request -> url());
            }
            
            $this -> sendResponse($this -> getAction($class) -> run($request));
        } catch (App\Exception $e) {
            if (!$this -> exceptionHandler) {
                throw $e;
            }
            
            $request -> setParam('exception', $e);
            $this -> sendResponse($this -> getAction($this -> exceptionHandler) -> run($request));
        }
    }
    
    /**
     * Send response.
     * 
     * @param Response|ResponseBody|string $response
     */
    protected function sendResponse($response)
    {
        $this -> prepareResponse($response) -> send($this -> listeners);
    }
    
    /**
     * Prepare response.
     * 
     * @param Response|ResponseBody|string $response
     * @return Response
     */
    protected function prepareResponse($response)
    {
        if (!$response instanceof Response) {
            $response = new Response($response);
        }
        
        $c = $this -> container -> get('config');
        
        return $response
            -> setCookiePath($c -> get(
                'cookie.path',
                $c -> get('routing.base', $this -> container -> get('router') -> getBase())
            ))
            -> setCookieDomain($c -> get('cookie.domain'));
    }
    
    /**
     * Find class name for request.
     * 
     * If Web Controller needs to do something more with request, 
     * this is first method to override.
     * 
     * @api
     * @return string|null
     */
    protected function findAction(Request $request)
    {
        $router = $this -> container -> get('router');
        $query = $request -> getQuery($router -> getBase(), $router -> getPrefix());
        
        list($id, $class, $params) = $router -> match($query, $request -> method());
        
        $request -> setParams($this -> getRequestParams($request, $query, $id, (array) $params));
        
        return $class;
    }
    
    /**
     * Prepare params to set for Request.
     * 
     * @api
     * @return array
     */
    protected function getRequestParams(Request $request, $query, $id, $params)
    {
        return $params + [
            'query' => $query,
            'action' => $id,
            'url' => $this -> container -> get('router') -> url($id, $params) -> setGet($request -> get())
        ];
    }
    
    /**
     * Try to create valid action instance.
     * 
     * @param string
     * @return \Vero\Web\ActionInterface
     */
    private function getAction($class)
    {
        if (!class_exists($class)) {
            throw new \LogicException('Action class '.$class.' not found (but defined as route)!');
        }
        
        $action = new $class($this -> container);
        
        if (!$action instanceof ActionInterface) {
            throw new \DomainException('Action class '.$class.' must implement Vero\Web\ActionInterface!');
        }
        
        return $action;
    }
}
