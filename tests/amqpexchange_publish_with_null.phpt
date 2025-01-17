--TEST--
AMQPExchange::publish() body with null-byte
--SKIPIF--
<?php
if (!extension_loaded("amqp")) print "skip";
if (!getenv("PHP_AMQP_HOST")) print "skip";
?>
--FILE--
<?php

class Foo
{
    private $bar = 'bar';
    protected $baz = 'baz';

    public function __construct($bar, $baz) {
        $this->bar = $bar;
        $this->baz = $baz;
    }
}

$cnn = new AMQPConnection(array('read_timeout' => 5));
$cnn->setHost(getenv('PHP_AMQP_HOST'));
$cnn->connect();
$channel = new AMQPChannel($cnn);

$q_name = 'test_' . bin2hex(random_bytes(32));

$q = new AMQPQueue($channel);
$q->setName($q_name);
$q->setFlags(AMQP_AUTODELETE);
$q->declareQueue();

$ex = new AMQPExchange($channel);
$orig1= new Foo('x1', 'y1');
$orig2= new Foo('x2', 'y2');
$s1 = serialize($orig1);
$s2 = serialize($orig2);


echo 'Orig 1:', PHP_EOL;
var_dump($orig1);
var_dump($s1);

echo PHP_EOL;

echo 'Orig 2:', PHP_EOL;
var_dump($orig2);
var_dump($s2);

echo PHP_EOL;


$ex->publish($s1, $q_name);
$ex->publish($s2, $q_name);


echo 'basic.get:', PHP_EOL;
$msg = $q->get();
var_dump($msg->getBody());
$restored = unserialize($msg->getBody());
var_dump($restored);

echo PHP_EOL;

$q->consume(function ($msg) {
    echo 'basic.consume:', PHP_EOL;

    var_dump($msg->getBody());
    $restored = unserialize($msg->getBody());
    var_dump($restored);

    return false;
});


?>
--EXPECT--
Orig 1:
object(Foo)#5 (2) {
  ["bar":"Foo":private]=>
  string(2) "x1"
  ["baz":protected]=>
  string(2) "y1"
}
string(60) "O:3:"Foo":2:{s:8:" Foo bar";s:2:"x1";s:6:" * baz";s:2:"y1";}"

Orig 2:
object(Foo)#6 (2) {
  ["bar":"Foo":private]=>
  string(2) "x2"
  ["baz":protected]=>
  string(2) "y2"
}
string(60) "O:3:"Foo":2:{s:8:" Foo bar";s:2:"x2";s:6:" * baz";s:2:"y2";}"

basic.get:
string(60) "O:3:"Foo":2:{s:8:" Foo bar";s:2:"x1";s:6:" * baz";s:2:"y1";}"
object(Foo)#8 (2) {
  ["bar":"Foo":private]=>
  string(2) "x1"
  ["baz":protected]=>
  string(2) "y1"
}

basic.consume:
string(60) "O:3:"Foo":2:{s:8:" Foo bar";s:2:"x2";s:6:" * baz";s:2:"y2";}"
object(Foo)#11 (2) {
  ["bar":"Foo":private]=>
  string(2) "x2"
  ["baz":protected]=>
  string(2) "y2"
}
