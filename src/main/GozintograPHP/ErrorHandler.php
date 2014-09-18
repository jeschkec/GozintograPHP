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

final class ErrorHandler
{
    public $logger;

    public function __construct($logger)
    {
        $this->logger = $logger;
    }

    /**
     *  The default error handler defaultErrorHandler writes the error message
     *  to STDERR as a csv string (separator is ;, enclosing is ").
     *
     *  <p>The string contains:</p>
     *  <ul>
     *      <li>An ISO-8601-formatted date, e.g. 2008-11-21T13:30:53+01:00</li>
     *      <li>The error level</li>
     *      <li>The error message</li>
     *      <li>The file name, in which the error occured</li>
     *      <li>The line number, on which the error occured</li>
     *  </ul>
     *
     *  @param  int     error number
     *  @param  string  error message
     *  @param  string  error file
     *  @param  int     error line
     *  @return int     1
     */
    public function setDefaultErrorHandler($number, $message, $file, $line)
    {
        $message = sprintf('[%s] %s at %s, Line %d', $number, $message, $file, $line);
        $this->logger->addError($message);
    }
}
