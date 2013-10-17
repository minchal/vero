<?php
/**
 * Simple autoload instance for Vero library.
 * 
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

require __DIR__.'/Vero/Loader/UniversalLoader.php';

$loader = new Vero\Loader\UniversalLoader();
$loader
    -> add('Vero', __DIR__.'/')
    -> register();
