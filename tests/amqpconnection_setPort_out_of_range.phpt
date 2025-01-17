--TEST--
AMQPConnection setPort with int out of range
--SKIPIF--
<?php
if (!extension_loaded("amqp")) print "skip";
if (!getenv("PHP_AMQP_HOST")) print "skip";
?>
--FILE--
<?php
$cnn = new AMQPConnection();
$cnn->setHost(getenv('PHP_AMQP_HOST'));
try {
    $cnn->setPort(1234567890);
} catch (Exception $e) {
    echo $e->getMessage();
}
?>
--EXPECT--
Parameter 'port' must be a valid port number between 1 and 65535.