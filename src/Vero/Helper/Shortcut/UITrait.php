<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Helper\Shortcut;

use Vero\Validate\Validator;
use Vero\UI\Form;
use Vero\UI\Question;
use Vero\UI\DoctrineListing;

/**
 * Shortcuts for creating UI objects.
 * 
 * Requires: DITrait
 */
trait UITrait
{
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
     * Create Form instance with current DI Container.
     * 
     * @param \Vero\Validate\Validator|\Vero\Validate\Container
     * @param array|null
     * @return Form
     */
    public function form($validator = null, $fields = null)
    {
        $form = Form::create($this -> getContainer());
        
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
        return Question::create($this -> getContainer());
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
}
