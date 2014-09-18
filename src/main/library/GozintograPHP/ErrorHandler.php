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
    public static function defaultErrorHandler($errNumber, $errMessage, $errFile, $errLine)
    {
        $errDetails = array(
            date('c'),              //  current ISO8601 formatted date
            $errNumber,             //  Error level number
            $errFile,               //  source file
            $errLine,               //  source line
            $errMessage,            //  The error message
        );

        if(false === is_resource(STDERR))
        {
            trigger_error('STDERR is not availiable', E_USER_ERROR);
        }

        fputcsv(STDERR, $errDetails, ';', '"');
        exit($errNumber);
    }


}
