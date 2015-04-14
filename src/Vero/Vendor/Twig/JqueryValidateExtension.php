<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig;

/**
 * Transform rule shortcuts and options from \Vero\Validate to html attributes
 * recognized by jquery.validate library.
 * 
 * @see http://bassistance.de/jquery-plugins/jquery-plugin-validation/
 */
class JqueryValidateExtension extends \Twig_Extension
{
    /**
     * Rules to methods of this class mapping.
     */
    protected $rules = [
        'chain'    => 'chain',
        'boolean'  => 'required',
        'integer'  => 'number',
        'number'   => 'number',
        'string'   => 'string',
        'email'    => 'string',
        'url'      => 'string',
        'idstr'    => 'idstr',
        'numstr'   => 'numstr',
        'regexp'   => 'regexp',
        'date'     => 'date',
        'datetime' => 'date',
        'time'     => 'time',
        'password' => 'password',
        'repeat'   => 'repeat',
        'set'      => 'required',
    ];
    
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'jquery_validate';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'validate' => new \Twig_SimpleFunction('validate', [$this, 'validate']),
            'resolveSetItems' => new \Twig_SimpleFunction('resolveSetItems', [$this, 'resolveSetItems']),
        ];
    }
    
    /**
     * Parse rule options and get items for "Set Rule".
     * If "Set Rule" is next rule after "Array Rule", get items from deeper level.
     * If "Set Rule" is in "Chain Rule", get irems from chain element.
     * 
     * @param array
     * @return array|\Vero\Validate\Rule\Set\SetInterface
     */
    public function resolveSetItems($validate)
    {
        if (isset($validate['options']['items'])) {
            return $validate['options']['items'];
        }
        
        if (isset($validate['options'])) {
            foreach ($validate['options'] as $row) {
                if (!is_array($row)) {
                    continue;
                }
                
                // from "Array Rule"
                if (isset($row['items'])) {
                    return $row['items'];
                }
                // from "Chain Rule"
                if (isset($row[1]['items'])) {
                    return $row[1]['items'];
                }
            }
        }
        
        return [];
    }
    
    /**
     * Parse rule and options and merge it with html attributes.
     * 
     * @param array
     * @param array
     * @return array
     */
    public function validate($validate, $attr = [])
    {
        if (isset($validate['rule']) && is_scalar($validate['rule']) &&
            isset($this -> rules[$validate['rule']])
        ) {
            if (!isset($validate['options'])) {
                $validate['options'] = [];
            }
            
            $attr = (array) $attr;
            $m = 'validate'.$this -> rules[$validate['rule']];
            return $this -> $m((array) $validate['options'], $attr);
        }
        
        return $attr;
    }
    
    protected function validateChain($opt, $attr)
    {
        foreach ($opt as $row) {
            if (is_array($row)) {
                list($rule, $opts) = $row;
            } else {
                $rule = $row;
                $opts = [];
            }
            
            if (isset($this -> rules[$rule])) {
                $m = 'validate'.$this -> rules[$rule];
                return $this -> $m((array) $opts, $attr);
            }
        }
        
        return $attr;
    }
    
    protected function validateRequired($opt, $attr)
    {
        if (!isset($opt['optional']) || !$opt['optional']) {
            $attr['required'] = 'required';
            $attr['class'] = (isset($attr['class']) ? $attr['class'].' ' : '') .'required'; // for CKEditor textareas
        }
        
        return $attr;
    }
    
    protected function validateNumber($opt, $attr)
    {
        // precision = 2, step = 0.01
        if (isset($opt['precision'])) {
            $attr['step'] = pow(10, -$opt['precision']);
        }
        
        $attr['data-rule-number'] = '';
        
        return $this -> validateMinMax($opt, $attr);
    }
    
    protected function validateString($opt, $attr)
    {
        if (isset($opt['min'])) {
            $attr['minlength'] = $opt['min'];
        }
        if (isset($opt['max'])) {
            $attr['maxlength'] = $opt['max'];
        }
        
        return $this -> validateRequired($opt, $attr);
    }
    
    protected function validateIdstr($opt, $attr)
    {
        $attr['data-rule-idstr'] = isset($opt['chars']) ? $opt['chars'] : '_-';
        return $this -> validateString($opt, $attr);
    }
    
    protected function validateNumstr($opt, $attr)
    {
        $attr['data-rule-numstr'] = '';
        return $this -> validateString($opt, $attr);
    }
    
    protected function validatePassword($opt, $attr)
    {
        if (!isset($opt['min'])) {
            $opt['min'] = 5;
        }
        if (!isset($opt['max'])) {
            $opt['max'] = 100;
        }
        
        $attr['data-rule-password'] = '';
        return $this -> validateString($opt, $attr);
    }
    
    protected function validateRegexp($opt, $attr)
    {
        if (isset($opt['pattern'])) {
            $attr['data-rule-regexp'] = json_encode(
                [
                    $opt['pattern'],
                    isset($opt['format']) ? $opt['format'] : $opt['pattern']
                ]
            );
        }
        
        return $this -> validateRequired($opt, $attr);
    }
    
    protected function validateDate($opt, $attr)
    {
        $attr['data-rule-date'] = '';
        return $this -> validateMinMax($opt, $attr);
    }
    
    protected function validateTime($opt, $attr)
    {
        $attr['data-rule-time'] = '';
        return $this -> validateMinMax($opt, $attr);
    }
    
    protected function validateRepeat($opt, $attr)
    {
        if (isset($opt['field'])) {
            $attr['data-rule-equalTo'] = 'input[name=\''.$opt['field'].'\']';
        }
        
        return $this -> validateRequired($opt, $attr);
    }
    
    protected function validateMinMax($opt, $attr)
    {
        if (isset($opt['min'])) {
            $attr['min'] = $opt['min'];
        }
        if (isset($opt['max'])) {
            $attr['max'] = $opt['max'];
        }
        
        return $this -> validateRequired($opt, $attr);
    }
}
