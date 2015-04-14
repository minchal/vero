<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Vendor\Doctrine\Query\Postgresql;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;

/**
 * CastFunction ::= "CAST" "(" ArithmeticExpression "AS" Identifier ")"
 */
class Cast extends FunctionNode
{
    /** @var \Doctrine\ORM\Query\AST\PathExpression */
    protected $field;
    
    /** @var string */
    protected $type;

    public function parse(Parser $parser)
    {
        $parser -> match(Lexer::T_IDENTIFIER);
        $parser -> match(Lexer::T_OPEN_PARENTHESIS);

        $this -> field = $parser -> ArithmeticExpression();

        $parser -> match(Lexer::T_AS);
        
        $this -> type = $parser -> getLexer() -> lookahead['value'];
        $parser -> match(Lexer::T_IDENTIFIER);
        
        $parser -> match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(SqlWalker $walker)
    {
        return sprintf(
            'CAST(%s AS %s)',
            $this -> field -> dispatch($walker),
            $this -> type
        );
    }
}
