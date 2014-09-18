<?php
/**
 *  GozintograPHP Demo Script
 *
 *  This ist for GozintograPHP documentation purposes <b>only</b>.
 *  Don't use this demo file standalone.
 *
 *  @link       http://gozintographp.org
 *  @package    GozintograPHP
 *  @author     Christoph Jeschke <gozintographp@cj-soft.de>
 *  @version	$Rev: 23 $ $Date: 2009-01-24 14:31:57 +0100 (Sa, 24. Jan 2009) $
 *  @copyright  Christoph Jeschke
 */

//  Abort if run standalone
die("This is only for GozintograPHP demonstration purposes. Aborting.\n");

/**
 *  include foo.php
 */
include 'foo.php';

/**
 *  include foo.php
 */
include "foo.php";

/**
 *  include_one bar.php
 */
include_once 'bar.php';

/**
 *  require foo.php
 */
require 'foo.php';

/**
 *  require_once bar.php
 */
require_once 'bar.php';

/**
 *  include file referenced by $foo
 */
include $foo;
