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

final class Application
{
    /**
     *  @var array  Key Zend Inclusion and Require Tokens
     */
    private $includeTokens =  array(
        T_REQUIRE, T_REQUIRE_ONCE,
        T_INCLUDE, T_INCLUDE_ONCE
    );
    
    /**
     *  @var array  Target Zend Tokens
     */
    private $valueTokens =   array(
        T_CONSTANT_ENCAPSED_STRING,
        T_STRING, T_VARIABLE
    );

    /**
     *  @var string Filename of source file, default is <b>NULL</b>
     */
    private $fileName =   null;
    
    /**
     *  @var array  Token structure from file, default is <b>NULL</b>
     */
    private $tokens = null;

    /**
     *  @var array  Relevant tokens, default is <b>NULL</b>
     */
    private $relevantTokens = array();
    
    /**
     *  @var array XML Structure, default is <b>NULL</b>
     */
    private $tokenAsXml = null;
    
    /**
     *  Get the created XML Structure
     *
     *  Default return value is <b>NULL</b>
     *  
     *  @return mixed
     */
    final public function getTokenAsXml()
    {
        return $this->tokenAsXml;
    }
    
    /**
     *  Replace quotes (',") by nothing
     *
     *  @param  string String probably with quotes
     *  @return string String without quotes
     *  @throws Exception
     */
    final public static function stripQuotes($quotedValue)
    {
        if(true === is_string($quotedValue))
        {
            return str_replace(array('"',"'"), '', $quotedValue);
        }

        return $quotedValue;
    }

    /**
     *  @param  string  $fileName   Source file to parse
     *  @throws Exception
     */
    public function __construct($fileName = null)
    {
        if(true === is_null($fileName))
        {
            throw new Exception('No filename was given', 1);
        }
        else
        {
            $this->fileName = $fileName;
        }
    }

    /**
     *  Show copyright information
     *
     *  Extends the version information with copyright informations
     *  @see    usage()
     */
    public static function getCopyright()
    {
        return  '(c) Christoph Jeschke, 2008-2014' . PHP_EOL .
                'Report bugs to <gozintograph@christoph-jeschke.de>' . PHP_EOL;
    }

    /**
     *  Show usage information
     *
     *  Uses the Zend_Console_Getopt getUsageMessage() method to show
     *  usage information
     *
     *  @param  object  $options Zend_Console_Getopt Object
     *  @see    Zend_Console_Getopt
     */
    public static function getUsage(\Zend_Console_Getopt $options)
    {
        return $options->getUsageMessage();
    }
    
    /**
	 *	Returns the string representation, aka the XML structure or
	 *  a NULL value
	 *	@return mixed	NULL is the default value
	 */
	public function __toString()
	{
		return $this->getTokenAsXml();
	}

    /**
     *  Read source file and return tokens
     *  
     *  @param string File name of source
     *  @return bool Status, false if something went wrong, true if not
     *  @throws Exception
     */
    final public function read($fileName = null)
    {
        if(false === is_null($fileName))
        {
            $this->fileName = $fileName;
        }
        
        if(true === is_null($this->fileName))
        {
            throw new Exception('No File to parse');
        }
        
        $fileContent    =   file_get_contents($this->fileName);
        
        if($fileContent === false)
        {
            throw new Exception('File could not be read');
        }
        
        if(strlen($fileContent) < 6)
        {
            throw new Exception('File ist a way to short');
        }
        
        $tokens     =   token_get_all($fileContent);
        
        if(sizeof($tokens) < 2)
        {
            throw new Exception('Could not extract enough tokens');
        }
        else
        {
            $this->tokens = $tokens;
        }
    }

    /**
     *  Parse the current tokens and create a pair of corresponding tokens
     *  @throws Exception
     */
    final public function parse()
    {
        $tokenStack    =   array();
        
        if(true === is_null($this->tokens))
        {
            throw new Exception('Nothing to do. Move along.');
        }
        
        $mergedTokens    =   array_merge($this->includeTokens, $this->valueTokens);
    
        foreach($this->tokens as $token)
        {
            if(true === in_array($token[0], $mergedTokens))
            {
                array_push($tokenStack, $token);
            }
        }
        
        $tokenStackSize = sizeof($tokenStack);
        
        if($tokenStackSize < 2)
        {
            throw new Exception('Not enough tokens to parse');
        }
        
        for($i = 0; $i < $tokenStackSize; $i++)
        {
            if(
                (true === in_array($tokenStack[$i][0], $this->includeTokens)) &&
                (true === in_array($tokenStack[$i+1][0], $this->valueTokens))
            )
            {
                array_push(
                    $this->relevantTokens,
                    array(
                        'method' => $this->stripQuotes($tokenStack["$i"][1]),
                        'target' => $this->stripQuotes($tokenStack[$i+1][1])
                    )
                );
            }
        }
    }

    /**
     *  @param  object  $xmlWriter   XMLWriter Object
     *  @return string                  XML Structure
     *  @throws Exception
     */
    final public function dump(\XMLWriter $xmlWriter)
    {
        if(false === $xmlWriter->openMemory())
        {
            throw new Exception('Can not open XMLWriter Memory');
        }
        
        if(false === $xmlWriter->startElement('source'))
        {
            throw new Exception('Can not start source element');
        }
        
        if(false === $xmlWriter->writeAttribute('file', $this->fileName))
        {
            throw new Exception('Can not write file attribute to source element');
        }
        
        foreach($this->relevantTokens as $token)
        {
            if(false === $xmlWriter->startElement('entry'))
            {
                throw new Exception("Can not start entry element for {$token['target']}");
            }
            
            if(false === $xmlWriter->writeAttribute('method', $token['method']))
            {
                throw new Exception("Can not write method attribute to {$token['target']}");
            }
            
            if(false === $xmlWriter->writeAttribute('target', $token['target']))
            {
                throw new Exception("Can not write target attribute to {$token['target']}");
            }
        
            if(false === $xmlWriter->endElement())
            {
                throw new Exception("Can not close entry element for {$token['target']}");
            }    
        }
        
        if(false === $xmlWriter->endElement())
        {
            throw new Exception('Can not close source element');
        }
        
        $this->tokenAsXml = $xmlWriter->outputMemory();
        
        return $this->tokenAsXml;
    }
}
?>
