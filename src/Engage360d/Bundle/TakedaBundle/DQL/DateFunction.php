<?php

namespace Engage360d\Bundle\TakedaBundle\DQL;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\Parser;

/**
 * DateFunction
 *
 * Allows Doctrine 2.0 Query Language to execute a MySQL DATE function
 * You must register this function adding it to ORM configuration.
 *
 * DATE(expr) : @link http://dev.mysql.com/doc/refman/5.5/en/date-and-time-functions.html#function_date
 *
 */
class DateFunction extends FunctionNode {
    /**
     * The datetime expression of the DATE DQL statement
     * @var mixed
     */
    protected $dateExpression;
    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     * @return string
     */
    public function getSql(SqlWalker $sqlWalker)
    {
        return 'DATE(' . $this->dateExpression->dispatch($sqlWalker) . ')';
    }
    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     */
    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->dateExpression = $parser->ArithmeticExpression();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

}