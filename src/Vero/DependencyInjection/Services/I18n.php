<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\DependencyInjection\Services;

use Vero\DependencyInjection\Container;
use Vero\DependencyInjection\LazyService;
use Vero\I18n\Translator;
use Vero\I18n\Backend\Xliff;

class I18n extends LazyService
{
    protected function create(Container $c)
    {
        return new Translator(
            new Xliff($c -> get('cache'), $c -> get('app') -> path('resources/i18n/')),
            $c -> get('config') -> get('language', 'en'),
            $c -> get('config') -> get('languages', [])
        );
    }
}
