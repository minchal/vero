<?php
/**
 * @author Michał Pawłowski <michal@pawlowski.be>
 */

namespace Vero\Web;

/**
 * Interface for view-like classes.
 */
interface ResponseBody
{
    /**
     * Response body can prepare itself.
     * E.g.: add custom headers, compile templates etc.
     * 
     * After that, all headers will be send.
     * 
     * @param Vero\Web\Response
     */
    public function prepare(Response $response, $buffer = null);
    
    /**
     * Do real echo yourself. All PHP buffers are disabled.
     * 
     * @return void
     */
    public function send();
}
