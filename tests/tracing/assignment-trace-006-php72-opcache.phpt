--TEST--
Test for tracing assignments in user-readable function traces (opcache)
--SKIPIF--
<?php
require __DIR__ . '/../utils.inc';
check_reqs('opcache');
?>
--INI--
xdebug.mode=trace
xdebug.start_with_request=no
xdebug.trace_format=0
xdebug.collect_return=0
xdebug.collect_assignments=1
--FILE--
<?php
require_once 'capture-trace.inc';

class foo {
	static public $foo;
	static private $var;
	static public $var2;
	static public $bar;
	static function test()
	{
		self::$foo = array();
		self::$foo[] = 42;
		self::$var = 'var';
		self::$var2 = self::$var;
		$id = 42;
		self::$foo[$id] = 44;

		self::$bar['test'] = array();
		$id = 'test';
		self::$bar[$id][] = 55;
	}
}
foo::test();

xdebug_stop_trace();
?>
--EXPECTF--
TRACE START [%d-%d-%d %d:%d:%d.%d]
                             => $tf = '%sxt%S' %s:%d
%w%f %w%d     -> foo::test() %sassignment-trace-006-php72-opcache.php:23
                             => self::foo = [] %sassignment-trace-006-php72-opcache.php:11
                             => self::foo[] = 42 %sassignment-trace-006-php72-opcache.php:12
                             => self::var = 'var' %sassignment-trace-006-php72-opcache.php:13
                             => self::var2 = 'var' %sassignment-trace-006-php72-opcache.php:14
                             => self::foo[42] = 44 %sassignment-trace-006-php72-opcache.php:16
                             => self::bar['test'] = [] %sassignment-trace-006-php72-opcache.php:18
                             => self::bar['test'][] = 55 %sassignment-trace-006-php72-opcache.php:20
%w%f %w%d     -> xdebug_stop_trace() %sassignment-trace-006-php72-opcache.php:25
%w%f %w%d
TRACE END   [%d-%d-%d %d:%d:%d.%d]
