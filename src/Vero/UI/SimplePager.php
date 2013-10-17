<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

/**
 * This Pager generates links for few pages back and forward 
 * from current page, first, previous, next and last pages.
 * 
 * Example:
 *  previous, 1, ..., 6, 7, 8 (current), 9, 10, 11, ..., 30, next.
 * 
 * Result for this example will be in array with keys:
 * 
 *  prev  => URL
 *  next  => URL
 *  current => 8
 *  pages => 
 *    1 => URL
 *    5 => null (placeholder)
 *    6 => URL
 *    7 => URL
 *    8 => URL
 *    9 => URL
 *    10 => URL
 *    11 => URL
 *    12 => null (placeholder)
 *    30 => URL
 * 
 * If any of links is not necessary, value in array is null.
 * (e.g. on first page do not show link to previous page)
 * 
 */
class SimplePager implements Pager
{
    protected $back;
    protected $forward;
    
    /**
     * Specify number of link back and forward from current page.
     * 
     * @param int
     * @param int
     */
    public function __construct($back = 2, $forward = 4)
    {
        $this -> back = $back;
        $this -> forward = $forward;
    }
    
    /**
     * {@inheritdoc}
     */
    public function create(Listing $listing)
    {
        $page = $listing -> page();
        $pages = $listing -> pagesCount();
        
        $links = [
            'prev'   => null,
            'next'   => null,
            'current'=> $listing -> page(),
            'pages'  => []
        ];
        
        if (!$pages) {
            return $links;
        }
        
        if ($page > 1) {
            $links['prev'] = $listing -> getPageUrl($page-1);
        }
        if ($page < $pages) {
            $links['next'] = $listing -> getPageUrl($page+1);
        }
        
        // first and last always
        $links['pages'][1] = $listing -> getPageUrl(1);
        
        $from = max($page-$this->back-1, 2);
        $to   = min($page+$this->forward+1, $pages-1);
        
        for ($i = $from; $i <= $to; $i++) {
            if (// show normal links, if placeholder "merges" only one link
                $i != 2 && $i != $pages-1 &&
                ($i == $page-$this->back-1 || $i == $page+$this->forward+1)
            ) {
                // placeholders
                $links['pages'][$i] = null;
            } else {
                $links['pages'][$i] = $listing -> getPageUrl($i);
            }
        }
        
        $links['pages'][$pages] = $listing -> getPageUrl($pages);
        
        return $links;
    }
}
