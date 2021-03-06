--TEST--
MongoDB\BSON\Binary::__set_state() requires unsigned 8-bit integer for type
--SKIPIF--
<?php require __DIR__ . "/../utils/basic-skipif.inc"?>
--FILE--
<?php
require_once __DIR__ . "/../utils/basic.inc";

echo throws(function() {
    MongoDB\BSON\Binary::__set_state(['data' => 'foobar', 'type' => -1]);
}, 'MongoDB\Driver\Exception\InvalidArgumentException'), "\n";

echo throws(function() {
    MongoDB\BSON\Binary::__set_state(['data' => 'foobar', 'type' => 256]);
}, 'MongoDB\Driver\Exception\InvalidArgumentException'), "\n";

?>
===DONE===
<?php exit(0); ?>
--EXPECT--
OK: Got MongoDB\Driver\Exception\InvalidArgumentException
Expected type to be an unsigned 8-bit integer, -1 given
OK: Got MongoDB\Driver\Exception\InvalidArgumentException
Expected type to be an unsigned 8-bit integer, 256 given
===DONE===
