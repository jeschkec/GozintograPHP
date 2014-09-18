#! /usr/bin/php
<?php
/**
 * This file is part of GozintograPHP
 *
 * PHP Version 5
 *
 *  @see        https://github.com/jeschkec/GozintograPHP
 *  @author     Christoph Jeschke <gozintographp@christoph-jeschke.de>
 *  @package    GozintograPHP
 *  @license    http://www.opensource.org/licenses/bsd-license.php BSD 3-clause License
 *  @copyright  (c) 2008-2014 Christoph Jeschke. All rights reserved
 */

/**
 * Zend_Console_Getopt is used to handle the console options
 * @todo The libraries have to be written into one single file
 */

namespace GozintograPHP {
    require dirname(dirname(dirname(__FILE__))) . '/vendor/autoload.php';
}

namespace Monolog {
    // setup monolog
    $logger = new Logger('gozintographp');
    $logger->pushHandler(new Handler\StreamHandler('php://stderr'), Logger::WARNING);
}

namespace GozintograPHP {

    $errorHandler = new ErrorHandler($logger);

    //  set default exception handler
    set_exception_handler(array('\GozintograPHP\ExceptionHandler', 'setDefaultExceptionHandler'));

    //  set default error handler
    set_error_handler(array($errorHandler, 'setDefaultErrorHandler'));

    //  be sure, register_argv_argc will be filled
    ini_set('register_argv_argc', 'On');

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
    try {
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
    } catch (\Zend_Console_Getopt_Exception $exception) {
        echo $exception->getUsageMessage();
        exit(3);
    }

    //  Should the help or the usage be shown?
    if ((true === isset($getopt->u)) or (true === isset($getopt->h))) {
        echo Application::getUsage($getopt);
        exit(0);
    }

    //  Should the copyright statement be shown?
    if (true === isset($getopt->c)) {
        echo Application::getCopyright();
        exit(0);
    }

    /**
     *  Gather the remaining arguments. There will be threaten as
     *  file names, which will be parsed.
     */
    try {
        $files   =   $getopt->getRemainingArgs();

        /**
         *  @throws  Zend_Console_Getopt_Exception
         */
        if (sizeof($files) == 0) {
            throw new Zend_Console_Getopt_Exception('No files given');
        }
    } catch (\Zend_Console_Getopt_Exception $exception) {
        echo $getopt->getUsageMessage();
        exit(3);
    }

    try {
        $xmlWriter = new \XMLWriter();

        /**
         *  @throws  Exception
         */
        if (false === $xmlWriter->openMemory()) {
            throw new Exception('Can not start XMLWriter memory');
        }

        /**
         *  @throws  Exception
         */
        if (false === $xmlWriter->startElement('map')) {
            throw new Exception('Can not start map element');
        }

        $xmlWriter->writeAttributeNS(
            'xmlns',
            'xsi',
            null,
            'https://raw.githubusercontent.com/jeschkec/GozintograPHP/master/src/main/ressources/schema/gozintographp.xsd'
        );

    } catch (Exception $exception) {
        fwrite(STDERR, $exception->getMessage());
        exit(3);
    }

    /**
     *
     */
    foreach ($files as $File) {
        try {
            $gozintographp = new Application($File);
            $gozintographp->read();
            $gozintographp->parse();
            $gozintographp->dump(new \XMLWriter);
        } catch (Exception $exception) {
            fwrite(STDERR, $exception->getMessage());
            exit(3);
        }

        $xmlWriter->writeRaw($gozintographp->getTokenAsXml());
    }

    try {
        /**
         *  @throws  Exception
         */
        if (false === $xmlWriter->endElement()) {
            throw new Exception('Can not end XMLWriter memory');
        }
    } catch (Exception $exception) {
        fwrite(STDERR, $exception->getMessage());
        exit(3);
    }

    //  write xml stream to STDOUT
    fwrite(STDOUT, $xmlWriter->outputMemory());
    exit(0);
}
