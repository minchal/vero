<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Twig\I18n;

use Twig_TokenParser;
use Twig_Token;

/**
 * Search and validate {% domain ... %} and {% enddomain %} tags.
 */
class DomainTokenParser extends Twig_TokenParser
{
    public function parse(Twig_Token $token)
    {
        $lineno = $token->getLine();
        $domain = $this->parser->getExpressionParser()->parseExpression();
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        $body = $this->parser->subparse(array($this, 'decideForEnd'), true);
        $this->parser->getStream()->expect(Twig_Token::BLOCK_END_TYPE);
        
        return new DomainNode($body, $domain, $lineno, $this->getTag());
    }
    
    public function getTag()
    {
        return 'domain';
    }
    
    public function decideForEnd($token)
    {
        return $token->test('enddomain');
    }
}
