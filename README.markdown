[![Build Status](https://travis-ci.org/jeschkec/GozintograPHP.svg?branch=master)](https://travis-ci.org/jeschkec/GozintograPHP)

# GozintograPHP - a gozintograph for PHP #

## Introduction ##
A gozintograph is a directed (asymmetric) graph taken from **manufacturing and production theory**
and shows the parts that goes (goz) into. It's name is derived from the fictional italian mathematican
*Zepartzat Gozinto*, literally means "the parts that goes into", coined by real world mathematican
[Andrew Vazsonyi](http://en.wikipedia.org/wiki/Andrew_V%C3%A1zsonyi]).

## Requirements ##
* [Composer](https://getcomposer.org/) will install all requirements.

## Optional Requirements for graphviz##

* Perl 5
* GraphViz
* XML::Parser
* XML::Simple
* XML::SimpleObject

## Installation ##
1. `git clone https://github.com/jeschkec/GozintograPHP`  
2. `cd GozintograPHP/`  
3. `composer install`  
4. `./src/bin/gozintogra.php src/demo/demo.php`  

## Usage ##
<pre>Usage: ./gozintogra.php [ options ] FILE [,FILE [, ...]]
--usage|-u           Usage - this text
--help|-h            Help (alias for --usage|-u)
--copyright|-c       Copyright statement</pre>

## Todo ##
 - Improve analysis of variable includes
 - Rework usage information
 - Rework `gozintogra.php-graphviz.pl`
    - Allow not only png for output
    - POD documentation
