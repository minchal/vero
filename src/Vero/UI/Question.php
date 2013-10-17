<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\UI;

/**
 * Form for Yes/No questions.
 */
class Question extends Form
{
    /**
     * Check, if user clicked "Yes" button.
     * 
     * @return boolean
     */
    public function yes()
    {
        $result = $this -> isSent() && $this -> request -> {$this->method}('yes');
        
        if ($result && $this -> autoFinish) {
            $this -> finish();
        }
        
        return $result;
    }
    
    /**
     * Check, if user clicked "No" button.
     * 
     * @return boolean
     */
    public function no()
    {
        return $this -> isSent() && $this -> request -> {$this->method}('no');
    }
}
