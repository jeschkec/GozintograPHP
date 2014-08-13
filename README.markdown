[![Build Status](https://travis-ci.org/jeschkec/GozintograPHP.svg?branch=master)](https://travis-ci.org/jeschkec/GozintograPHP)

# GozintograPHP - a gozintograph for PHP #

## Introduction ##
A gozintograph is a directed (asymmetric) graph taken from **manufacturing and production theory**
and shows the parts that goes (goz) into. It's name is derived from the fictional italian mathematican
*Zepartzat Gozinto*, literally means "the parts that goes into", coined by real world mathematican
Andrew Vazsonyi.

## Requirements ##
 - `PHP 5.1.2` or newer
 - `Zend_Console_Getopt` (bundled)
 - `Perl 5` (for Perl image generator)
 - `GraphViz` (for Perl image generator)
 - `XML::Parser` (for Perl image generator)
 - `XML::Simple` (for Perl image generator)
 - `XML::SimpleObject` (for Perl image generator)

## Installation ##
Download / pull the source and extract it to a directory for your choice. You might add it
to your `$PATH` for more convenient usage.

## Usage ##
<pre>Usage: ./gozintogra.php [ options ] FILE [,FILE [, ...]]
--usage|-u           Usage - this text
--help|-h            Help (alias for --usage|-u)
--version|-V         Version
--copyright|-c       Copyright statement</pre>

## Todo ##
 - Improve analysis of variable includes
 - Rework usage information
 - Rework `gozintogra.php-graphviz.pl`
    - Allow not only png for output
    - POD documentation
