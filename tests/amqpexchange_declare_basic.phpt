--TEST--
AMQPExchange
--SKIPIF--
<?php
if (!extension_loaded("amqp")) print "skip";
if (!getenv("PHP_AMQP_HOST")) print "skip";
?>
--FILE--
<?php
$cnn = new AMQPConnection();
$cnn->setHost(getenv('PHP_AMQP_HOST'));
$cnn->connect();

$ch = new AMQPChannel($cnn);

$ex = new AMQPExchange($ch);
var_dump($ex->setName("exchange-" . bin2hex(random_bytes(32))));
var_dump($ex->setType(AMQP_EX_TYPE_FANOUT));
var_dump($ex->declareExchange());
?>
--EXPECT--
NULL
NULL
NULL
