#! /usr/bin/php
<?php
/**
 *  @see        https://github.com/jeschkec/GozintograPHP
 *  @author     Christoph Jeschke <gozintographp@christoph-jeschke.de>
 *  @package    GozintograPHP
 *  @license    BSD Style License https://github.com/jeschkec/GozintograPHP/blob/master/LICENSE
 *  @copyright  (c) 2008 Christoph Jeschke
 */

namespace GozintograPHP;

/**
 *  defaultExceptionHandler bubbles up a uncought exception to the
 *  defautlt error handler defaultErrorHandler()
 *
 *  @see    defaultErrorHandler
 *  @param  object  Exception object
 */
function defaultExceptionHandler($exception)
{
    trigger_error($exception->getMessage());
}

//  set default exception handler
set_exception_handler('\GozintograPHP\defaultExceptionHandler');

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
function defaultErrorHandler($errNumber, $errMessage, $errFile, $errLine)
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

//  set default error handler
set_error_handler('\GozintograPHP\defaultErrorHandler');

//  be sure, register_argv_argc will be filled
ini_set('register_argv_argc', 'On');

/**
 *  Show usage information
 *
 *  Uses the Zend_Console_Getopt getUsageMessage() method to show
 *  usage information
 *
 *  @param  object  $options Zend_Console_Getopt Object
 *  @see    Zend_Console_Getopt
 */
function usage(\Zend_Console_Getopt $options)
{
    $options->getUsageMessage();
}

/**
 *  Show copyright information
 *
 *  Extends the version information with copyright informations
 *  @see    usage()
 */
function copyright()
{
    echo '(c) Christoph Jeschke, 2014', PHP_EOL;
    echo 'Report bugs to <gozintograph@christoph-jeschke.de>', PHP_EOL;
}

/**
 * Zend_Console_Getopt is used to handle the console options
 * @todo The libraries have to be written into one single file
 */
require 'vendor/autoload.php';

/**
 *  Set up console option (arg) handling
 *  It takes several arguments:
 *  <ul>
 *  <li>
 *      <kbd>--usage</kbd> or <kbd>-u</kbd> shows the default usage information
 *  </li>
 *  <li>
 *      <kbd>--help</kbd> or <kbd>-h</kbd> is an alias to <kbd>--usage</kbd>
 *  </li>
 *  <li>
 *      <kbd>--copyright</kbd> or <kbd>-c</kbd> prints out a copyright statement
 *  </li>
 *  <li>
 *      All additional arguments will be threaten as file names
 *  </ul>
 */
try
{
    $options = array(
        'usage|u' => 'Usage - this text',
        'help|h' => 'Help (alias for --usage|-u)',
        'copyright|c' => 'Copyright statement'
    );

    //  $objOptions will be a Zend_Console_Getopt object
    $getopt = new \Zend_Console_Getopt($options);

    //  set explict case sensitiveness
    $getopt->setOption('ignoreCase', false);

    //  Parse options
    $getopt->parse();
}
catch(\Zend_Console_Getopt_Exception $exception)
{
    echo $exception->getUsageMessage();
    exit(3);
}

//  Should the help or the usage be shown?
if( (true === isset($getopt->u)) or (true === isset($getopt->h)) )
{
    usage($getopt);
    exit(0);
}

//  Should the copyright statement be shown?
if(true === isset($getopt->c))
{
    copyright();
    exit(0);
}

/**
 *  Gather the remaining arguments. There will be threaten as
 *  file names, which will be parsed.
 */
try
{
    $files   =   $getopt->getRemainingArgs();

    /**
     *  @throws  Zend_Console_Getopt_Exception
     */
    if(sizeof($files) == 0)
    {
        throw new Zend_Console_Getopt_Exception('No files given');
    }
}
catch(\Zend_Console_Getopt_Exception $exception)
{
    echo $getopt->getUsageMessage();
    exit(3);
}

try
{
    $xmlWriter = new \XMLWriter();

    /**
     *  @throws  Exception
     */
    if(false === $xmlWriter->openMemory())
    {
        throw new Exception('Can not start XMLWriter memory');
    }

    /**
     *  @throws  Exception
     */
    if(false === $xmlWriter->startElement('map'))
    {
        throw new Exception('Can not start map element');
    }

    $xmlWriter->writeAttributeNS('xmlns', 'xsi', null, 'https://raw.githubusercontent.com/jeschkec/GozintograPHP/master/lib/gozintographp.xsd');
}
catch(Exception $exception)
{
    fwrite(STDERR, $exception->getMessage());
    exit(3);
}

/**
 *
 */
foreach($files as $File)
{
    try
    {
        $gozintographp = new GozintograPHP($File);
        $gozintographp->read();
        $gozintographp->parse();
        $gozintographp->dump(new \XMLWriter);
    }
    catch(Exception $exception)
    {
        fwrite(STDERR, $exception->getMessage());
        exit(3);
    }

    $xmlWriter->writeRaw($gozintographp->getTokenAsXml());
}

try
{
    /**
     *  @throws  Exception
     */
    if(false === $xmlWriter->endElement())
    {
        throw new Exception('Can not end XMLWriter memory');
    }
}
catch(Exception $exception)
{
    fwrite(STDERR, $exception->getMessage());
    exit(3);
}

//  write xml stream to STDOUT
fwrite(STDOUT, $xmlWriter->outputMemory());
exit(0);
?>
