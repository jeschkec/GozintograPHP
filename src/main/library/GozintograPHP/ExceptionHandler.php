<?php
/**
 *  GozintograPHP
 *
 *  @see        https://github.com/jeschkec/GozintograPHP
 *  @author     Christoph Jeschke <gozintographp@christoph-jeschke.de>
 *  @package    GozintograPHP
 *  @license    BSD Style License https://github.com/jeschkec/GozintograPHP/blob/master/LICENSE
 *  @copyright  (c) 2008 Christoph Jeschke
 */

namespace GozintograPHP;

final class ExceptionHandler
{
    /**
     *  defaultExceptionHandler bubbles up a uncought exception to the
     *  defautlt error handler defaultErrorHandler()
     *
     *  @see    defaultErrorHandler
     *  @param  object  Exception object
     */
    public static function defaultExceptionHandler($exception)
    {
        trigger_error($exception->getMessage());
    }
}
