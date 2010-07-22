GozintograPHP - a gozintograph for PHP
======================================

Visit [GozintograPHP.org](http://gozintographp.org/ "Project Homepage")!

Introduction
------------
A gozintograph is a directed (asymmetric) graph taken from **manufacturing and procution theory** 
and shows the parts that goes (goz) into. It's name is derived from the fictional italian mathematican 
*Zepartzat Gozinto*, literally means "the parts that goes into", coined by real world mathematican
Andrew Vazsonyi.

Requirements
------------
 - `PHP 5.1.2`
 - `Zend_Console_Getopt` (bundled)
 - `Perl 5` (for Perl image generator)
 - `GraphViz` (for Perl image generator)
 - `XML::Parser` (for Perl image generator)
 - `XML::Simple` (for Perl image generator)
 - `XML::SimpleObject` (for Perl image generator)

Installation
------------
Download / checkout the complete source and extract it to a directory for your choice. You might add it
to your `$PATH` for more convenient usage.

Usage
-----
<pre>Usage: ./gozintogra.php [ options ] FILE [,FILE [, ...]]
--usage|-u           Usage - this text
--help|-h            Help (alias for --usage|-u)
--version|-V         Version
--copyright|-c       Copyright statement</pre>


