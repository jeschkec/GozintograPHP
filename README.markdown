[![Build Status](https://travis-ci.org/jeschkec/GozintograPHP.svg?branch=master)](https://travis-ci.org/jeschkec/GozintograPHP)

# GozintograPHP - a gozintograph for PHP #

## Introduction ##
A gozintograph is a directed (asymmetric) graph taken from **manufacturing and production theory**
and shows the parts that goes (goz) into. It's name is derived from the fictional italian mathematican
*Zepartzat Gozinto*, literally means "the parts that goes into", coined by real world mathematican
Andrew Vazsonyi.

## Installation ##
* `git clone https://github.com/jeschkec/GozintograPHP`
* `cd GozintograPHP/`
* `composer install`
* `./src/bin/

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
