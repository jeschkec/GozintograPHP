#! /usr/bin/php
<?php
/**
 *  @see        <http://gozintographp.org>
 *  @author     Christoph Jeschke <jeschkec@gozintographp.org>
 *  @version    $Rev: 21 $ $Date: 2009-01-24 14:29:27 +0100 (Sa, 24. Jan 2009) $
 *  @package    GozintograPHP
 *  @license    BSD Style License http://gozintographp.org/#License
 *  @copyright  (c) 2008 Christoph Jeschke
 *  @tutorial   http://gozintographp.org/#Intro
 */

/**
 *  defaultExceptionHandler bubbles up a uncought exception to the
 *  defautlt error handler defaultErrorHandler()
 *
 *  @see    defaultErrorHandler
 *  @param  object  Exception object
 */
function defaultExceptionHandler($objException)
{
    trigger_error($objException->getMessage());
}

//  set default exception handler
set_exception_handler('defaultExceptionHandler');

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
 *  @param  int     Error Number
 *  @param  string  Error Message
 *  @param  string  Error file
 *  @param  int     Error line
 *  @return int     1
 */
function defaultErrorHandler($errNumber, $errMessage, $errFile, $errLine)
{
    $errDetails =   array(
        date('c'),              //  current ISO8601 formatted date
        $errNumber,             //  Error level number
        $errFile,               //  source file
        $errLine,               //  source line
        $errMessage,            //  The error message
    );

    if(false === is_ressource(STDERR)) 
    {
        trigger_error('STDERR is not availiable', E_USER_ERROR);
    }
    
    fputcsv(STDERR, $errDetails, ';', '"');
    exit($errNumber);
}

//  set default error handler
set_error_handler('defaultErrorHandler');

//  expand include path to lib/ directory
ini_set('include_path', ini_get('include_path') . PATH_SEPARATOR . dirname(__FILE__) . '/lib');

//  be sure, register_argv_argc will be filled
ini_set('register_argv_argc', 'On');

/**
 *  Show usage information
 *
 *  Uses the Zend_Console_Getopt getUsageMessage() method to show
 *  usage information
 *
 *  @param  object  $objOptions Zend_Console_Getopt Object
 *  @see    Zend_Console_Getopt
 */
function usage(Zend_Console_Getopt $objOptions)
{
    $objOptions->getUsageMessage();
}

/**
 *  Show version information
 */
function version()
{
    printf('GozintograPHP %s%s', '$Rev: 21 $', PHP_EOL);
}

/**
 *  Show copyright information
 *
 *  Extends the version information with copyright informations
 *  @see    usage()
 */
function copyright()
{
    version();
    printf('(c) Christoph Jeschke%s', PHP_EOL);
    printf('Report bugs to <jeschkec@gozintographp.org>%s', PHP_EOL);
}

/**
 *  GozintograPHP is the main library and does all the work
 */
require_once('GozintograPHP.class.php');


/**
 *  Zend_Console_Getopt is used to handle the console options
 */
require_once('Zend/Console/Getopt.php');

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
 *      <kbd>--version</kbd> or <kbd>-V</kbd> shows the current version
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
    $arrOptions =   array(
                        'usage|u'       =>  'Usage - this text',
                        'help|h'        =>  'Help (alias for --usage|-u)',
                        'version|V'     =>  'Version',
                        'copyright|c'   =>  'Copyright statement'
                    );

    //  $objOptions will be a Zend_Console_Getopt object
    $objOptions =   new Zend_Console_Getopt($arrOptions);

    //  set explict case sensitiveness
    $objOptions->setOption('ignoreCase', false);

    //  Parse options
    $objOptions->parse();
}
catch(Zend_Console_Getopt_Exception $objException)
{
    echo $objException->getUsageMessage();
    exit(3);
}
;

//  Should the help or the usage be shown?
if( (true === isset($objOptions->u)) or (true === isset($objOptions->h)) )
{
    usage($objOptions);
    exit(0);
}

//  Should the version information be shown?
if(true === isset($objOptions->V))
{
    version();
    exit(0);
}

//  Should the copyright statement be shown?
if(true === isset($objOptions->c))
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
    $arrFiles   =   $objOptions->getRemainingArgs();

    /**
     *  @throws  Zend_Console_Getopt_Exception
     */
    if(sizeof($arrFiles) == 0)
    {
        throw new Zend_Console_Getopt_Exception('No Files given');
    }
}
catch(Zend_Console_Getopt_Exception $objException)
{
    echo $objOptions->getUsageMessage();
    exit(3);
}

try
{
    $objXmlWriter   =   new XMLWriter();

    /**
     *  @throws  Exception
     */
    if(false === $objXmlWriter->openMemory())
    {
        throw new Exception('Can not start XMLWriter memory');
    }

    /**
     *  @throws  Exception
     */
    if(false === $objXmlWriter->startElement('map'))
    {
        throw new Exception('Can not start map element');
    }
}
catch(Exception $objException)
{
    fwrite(STDERR, $objException->getMessage());
    exit(3);
}

/**
 *
 */
foreach($arrFiles as $File)
{
    try
    {
        $objGozinto =   new GozintograPHP($File);
        $objGozinto->read();
        $objGozinto->parse();
        $objGozinto->dump(new XMLWriter);
    }
    catch(Exception $objException)
    {
        fwrite(STDERR, $objException->getMessage());
        exit(3);
    }

    $objXmlWriter->writeRaw($objGozinto->getTokXmlStructure());
}

try
{
    /**
     *  @throws  Exception
     */
    if(false === $objXmlWriter->endElement())
    {
        throw new Exception('Can not end XMLWriter memory');
    }
}
catch(Exception $objException)
{
    fwrite(STDERR, $objException->getMessage());
    exit(3);
}

//  write xml stream to STDOUT
fwrite(STDOUT, $objXmlWriter->outputMemory());
exit(0);

?>
