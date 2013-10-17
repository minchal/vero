<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Validate\Rule\Set;

/**
 * Set proxy for array.
 */
class ArraySet extends CallbackSet
{
    /**
     * @inheritdoc
     */
    public function __construct(array $data)
    {
        $d = [];
        
        foreach ($data as $k => $v) {
            $d[] = ['key' => $k, 'value' => $v];
        }
        
        parent::__construct(
            $d,
            function ($item) {
                return $item['key'];
            },
            function ($item) {
                return $item['value'];
            }
        );
    }
}
