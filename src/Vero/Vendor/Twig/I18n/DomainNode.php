<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig\I18n;

use Twig_Node;
use Twig_NodeInterface;
use Twig_Node_Expression;
use Twig_Compiler;

/**
 * Compile {% domain ... %} and {% enddomain %} tags.
 */
class DomainNode extends Twig_Node
{
    public function __construct(Twig_NodeInterface $body, Twig_Node_Expression $domain, $lineno, $tag = null)
    {
        parent::__construct(['domain' => $domain, 'body' => $body], [], $lineno, $tag);
    }

    public function compile(Twig_Compiler $compiler)
    {
        $compiler
            ->addDebugInfo($this)
            ->write("\$context['i18n'] -> inDomain(")
            ->subcompile($this->getNode('domain'))
            ->raw(");\n")
            ->subcompile($this->getNode('body'))
            ->write("\$context['i18n'] -> outDomain();\n");
    }
}
