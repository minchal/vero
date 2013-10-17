<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

use IteratorAggregate;
use Countable;
use ArrayIterator;
use Vero\Routing\URL;

/**
 * Class to creating listings with:
 *  - pagination
 *  - order changing
 *  - items on page number
 * 
 * Set current page number (counting from 1) and order key.
 * Get limit() and offset() values and order() SQL part.
 * Create all pagination and orders links.
 */
class Listing implements IteratorAggregate, Countable
{
    /**
     * @var Pager
     */
    protected $pager;
    
    protected $onPage = 10;
    protected $page = 1;
    protected $count;
    
    protected $order;
    protected $orders = [];
    
    protected $pageUrl;
    protected $pageUrlKey;
    protected $pageUrlMethod;
    
    protected $orderUrl;
    protected $orderUrlKey;
    protected $orderUrlMethod;
    
    protected $items;
    
    protected $urlToken;
    
    /**
     * Create instance optionaly with custom Pager builder instance.
     * 
     * @param Pager|null
     */
    public function __construct(Pager $pager = null)
    {
        if (!$pager) {
            $pager = new SimplePager();
        }
        
        $this -> pager = $pager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        if (is_array($this -> items)) {
            return new ArrayIterator($this -> items);
        }
        
        return $this -> items;
    }
    
    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this -> items);
    }
    
    /**
     * Get limit for current page.
     * 
     * @return int
     */
    public function limit()
    {
        return $this -> onPage;
    }
    
    /**
     * Get offset for current page.
     * 
     * @return int
     */
    public function offset()
    {
        return ($this->page() - 1) * $this -> onPage;
    }
    
    /**
     * Return matched order SQL part from current order key or first correct.
     * 
     * @return mixed
     */
    public function order()
    {
        if (!$this -> orders) {
            return null;
        }
        
        return isset($this -> orders[$this -> order])
            ? $this -> orders[$this -> order] : reset($this -> orders);
    }
    
    /**
     * Return current order key.
     * 
     * @return mixed
     */
    public function orderKey()
    {
        return $this -> order;
    }
    
    /**
     * Get items on current page.
     * 
     * @return array|\Iterator
     */
    public function items()
    {
        return $this -> items;
    }
    
    /**
     * Get current page.
     * 
     * @return int
     */
    public function page()
    {
        return max(1, min($this -> page, $this -> pagesCount()));
    }
    
    /**
     * Get number of available pages.
     * 
     * @return int
     */
    public function pagesCount()
    {
        return ceil($this->count / $this->onPage);
    }
    
    /**
     * Get pagination items created by Pager.
     * 
     * @return array
     */
    public function pages()
    {
        return $this -> pager -> create($this);
    }
    
    /**
     * Alias for getOrderUrl().
     * 
     * @see getOrderUrl()
     * @param type $key
     * @return string|URL
     */
    public function orderUrl($key)
    {
        return $this -> getOrderUrl($key);
    }
    
    /**
     * Set total count numer.
     * 
     * @param int
     * @retrun self
     */
    public function setPage($page)
    {
        $this -> page = $page < 1 ? 1 : (int) $page;
        return $this;
    }
    
    /**
     * Set total items count number.
     * 
     * @param int
     * @retrun self
     */
    public function setCount($count)
    {
        $this -> count = $count < 0 ? 0 : (int) $count;
        return $this;
    }
    
    /**
     * Set items on page number.
     * 
     * @param int
     * @retrun self
     */
    public function setOnPage($onPage)
    {
        $this -> onPage = $onPage < 1 ? 1 : (int) $onPage;
        return $this;
    }
    
    /**
     * Set current order key.
     * 
     * @param mixed
     * @retrun self
     */
    public function setOrder($order)
    {
        $this -> order = $order;
        return $this;
    }
    
    /**
     * Set available orders array.
     * 
     * @param array
     * @retrun self
     */
    public function setOrders(array $orders)
    {
        $this -> orders = $orders;
        return $this;
    }
    
    /**
     * Set basic URL to create page links.
     * 
     * If $url is instance of URL with action part created by Router, 
     * then $param should be name of action param to replace.
     * 
     * If page number is passed by GET query (e.g. ?page=...),
     * then $method should be 'setGet'.
     * 
     * @param string|URL
     * @param string|null
     * @param string
     * @return self
     */
    public function setPageUrl($url, $param = null, $method = 'replace')
    {
        $this -> pageUrl = $url;
        $this -> pageUrlKey = $param ? $param : $this -> urlToken();
        $this -> pageUrlMethod = $method;
        
        return $this;
    }
    
    /**
     * Set basic URL to create order links.
     * 
     * @see setPageUrl()
     * @param string|URL
     * @param string|null
     * @param string
     * @return self
     */
    public function setOrderUrl($url, $param = null, $method = 'replace')
    {
        $this -> orderUrl = $url;
        $this -> orderUrlKey = $param ? $param : $this -> urlToken();
        $this -> orderUrlMethod = $method;
        
        return $this;
    }
    
    /**
     * Set common URL for pages and orders.
     * 
     * @param URL
     * @return self
     */
    public function setUrl(URL $url, $page = 'page', $order = 'order')
    {
        $this -> setPageUrl($url, $page)
              -> setOrderUrl($url, $order);
        
        return $this;
    }
    
    /**
     * Set items on current page.
     * 
     * @param array|\Traversable
     * @return self
     */
    public function setItems($items)
    {
        if (!$items instanceof \Traversable && !is_array($items)) {
            throw new \DomainException('Items data for listing must be array or \Traversable instance!');
        }
        
        $this -> items = $items;
        return $this;
    }
    
    /**
     * Get random (but constant for instance) token to replace in string URLs.
     * 
     * @see setPageUrl()
     * @return string
     */
    public function urlToken()
    {
        if (!$this -> urlToken) {
            $this -> urlToken = '___Vero_UI_Listing::TOKEN_'.sha1(uniqid(mt_rand(), true)).'___';
        }
        
        return $this -> urlToken;
    }
    
    /**
     * Get URL to specified page.
     * 
     * @param int
     * @return string|URL
     */
    public function getPageUrl($page)
    {
        return $this -> replaceUrl(
            $this -> pageUrl,
            $this -> pageUrlKey,
            $page,
            $this -> pageUrlMethod
        );
    }
    
    /**
     * Get URL to specified order.
     * 
     * @param int
     * @return string|URL
     */
    public function getOrderUrl($order)
    {
        if (!isset($this -> orders[$order])) {
            throw new \DomainException('Order key '.$order.' not defined!');
        }
        
        return $this -> replaceUrl(
            $this -> orderUrl,
            $this -> orderUrlKey,
            $order,
            $this -> orderUrlMethod
        );
    }
    
    /**
     * Replace key in URL or string.
     * When replacing in URL instance method can be 'replace' or 'setGet'.
     * 
     * @param URL|string $url
     * @param string
     * @param string
     * @param string
     * @return type
     */
    protected function replaceUrl($url, $key, $value, $method = 'replace')
    {
        if ($url instanceof URL) {
            return $url -> copy() -> $method($key, $value);
        }
        
        return str_replace($key, $value, $url);
    }
}
