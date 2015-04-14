<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine;

use Vero\DependencyInjection\Dependent;
use Vero\Helper\Shortcut;

class DependentEntityListener implements Dependent
{
    use Shortcut\DITrait;
}
