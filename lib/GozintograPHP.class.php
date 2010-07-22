<?php
/**
 *  GozintograPHP
 *
 *  @see        <http://gozintographp.org>
 *  @author     Christoph Jeschke <jeschkec@gozintographp.org>
 *  @version    $Rev: 22 $ $Date: 2009-01-24 14:31:39 +0100 (Sa, 24. Jan 2009) $
 *  @package    GozintograPHP
 *  @license    BSD Style License http://gozintographp.org/#License
 *  @copyright  (c) 2008 Christoph Jeschke
 *  @tutorial   http://gozintographp.org/#Intro
 */
final class GozintograPHP
{
    /**
     *  @var array  Key Zend Inclusion and Require Tokens
     */
    private $tokIncludes    =       array(
                                        T_REQUIRE, T_REQUIRE_ONCE,
                                        T_INCLUDE, T_INCLUDE_ONCE
                                    );
    
    /**
     *  @var array  Target Zend Tokens
     */
    private $tokValues          =   array(
                                        T_CONSTANT_ENCAPSED_STRING, 
                                        T_STRING, T_VARIABLE
                                    );
    
    /**
     *  @var string Filename of source file, default is <b>NULL</b>
     */
    private $fileName           =   null;
    
    /**
     *  @var array  Token structure from file, default is <b>NULL</b>
     */
    private $tokStructure       =   null;
    
    /**
     *  @var array  Relevant tokens, default is <b>NULL</b>
     */
    private $tokRelevant        =   array();
    
    /**
     *  @var array XML Structure, default is <b>NULL</b>
     */
    private $tokXmlStructure    =   null;
    
    /**
     *  Get the created XML Structure
     *
     *  Default return value is <b>NULL</b>
     *  
     *  @return mixed
     */
    final public function getTokXmlStructure()
    {
        return $this->tokXmlStructure;
    }
    
    /**
     *  Replace quotes (',") by nothing
     *
     *  @param  string String probably with quotes
     *  @return string String without quotes
     *  @throws Exception
     */
    final public static function stripAllQuotes($stringQuotedValue) 
    {
        if(false === is_string($stringQuotedValue))
        {
            throw new Exception('$stringQuotedValue is not an string');
        }

        return str_replace(array('"',"'"), '', $stringQuotedValue);
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
	 *	Returns the string representation, aka the XML structure or
	 *  a NULL value
	 *	@return mixed	NULL is the default value
	 */
	public function __toString()
	{
		return $this->getTokXmlStructure();
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
        
        $tokAll     =   token_get_all($fileContent);
        
        if(sizeof($tokAll) < 2)
        {
            throw new Exception('Could not extract enough tokens');
        }
        else
        {
            $this->tokStructure = $tokAll;
        }
    }

    /**
     *  Parse the current tokens and create a pair of corresponding tokens
     *  @throws Exception
     */
    final public function parse()
    {
        $tokTemp    =   array();
        
        if(true === is_null($this->tokStructure))
        {
            throw new Exception('Nothing to do. Move along.');
        }
        
        $tokMergedTokens    =   array_merge($this->tokIncludes, $this->tokValues);
    
        foreach($this->tokStructure as $tokToken)
        {
            if(true === in_array($tokToken[0], $tokMergedTokens))
            {
                array_push($tokTemp, $tokToken);
            }
        }
        
        $tokTempSize = sizeof($tokTemp);
        
        if($tokTempSize < 2)
        {
            throw new Exception('Not enough tokens to parse');
        }
        
        for($i = 0; $i < $tokTempSize; $i++)
        {
            if(
                (true === in_array($tokTemp[$i][0], $this->tokIncludes)) &&
                (true === in_array($tokTemp[$i+1][0], $this->tokValues))
            )
            {
                array_push(
                    $this->tokRelevant, 
                    array(
                        'method' => $this->stripAllQuotes($tokTemp["$i"][1]), 
                        'target' => $this->stripAllQuotes($tokTemp[$i+1][1])
                    )
                );
            }
        }
    }

    /**
     *  @param  object  $objXMLWriter   XMLWriter Object
     *  @return string                  XML Structure
     *  @throws Exception
     */
    final public function dump(XMLWriter $objXMLWriter)
    {
        if(false === $objXMLWriter->openMemory())
        {
            throw new Exception('Can not open XMLWriter Memory');
        }
        
        if(false === $objXMLWriter->startElement('source'))
        {
            throw new Exception('Can not start source element');
        }
        
        if(false === $objXMLWriter->writeAttribute('file', $this->fileName))
        {
            throw new Exception('Can not write file attribute to source element');
        }
        
        foreach($this->tokRelevant as $tokRelevantToken)
        {
            if(false === $objXMLWriter->startElement('entry'))
            {
                throw new Exception("Can not start entry element for {$tokRelevantToken['target']}");
            }
            
            if(false === $objXMLWriter->writeAttribute('method', $tokRelevantToken['method']))
            {
                throw new Exception("Can not write method attribute to {$tokRelevantToken['target']}");
            }
            
            if(false === $objXMLWriter->writeAttribute('target', $tokRelevantToken['target']))
            {
                throw new Exception("Can not write target attribute to {$tokRelevantToken['target']}");
            }
        
            if(false === $objXMLWriter->endElement())
            {
                throw new Exception("Can not close entry element for {$tokRelevantToken['target']}");
            }    
        }
        
        if(false === $objXMLWriter->endElement())
        {
            throw new Exception('Can not close source element');
        }
        
        $this->tokXmlStructure = $objXMLWriter->outputMemory();
        
        return $this->tokXmlStructure;
    }
}
?>