# Running Commands

# Listing databases & collections

```php
<?php

/* Construct the MongoDB Manager */
$manager = new MongoDB\Manager("mongodb://localhost:27017");


$listdatabases = new MongoDB\Command(array("listDatabases" => 1));
$retval        = $manager->executeCommand("admin", $listdatabases);
$databases     = $retval->getResponseDocument();

foreach($databases["databases"] as $database) {
	echo $database->name, "\n";

	$listcollections = new MongoDB\Command(array("listCollections" => 1));
	$retval          = $manager->executeCommand($database->name, $listcollections);
	$collections     = $retval->getResponseDocument();
	foreach($collections["collections"] as $collection) {
		echo "\t- ", $collection->name, "\n";
	}
}

?>
```

# Create a User

```php
<?php


/* Construct the MongoDB Manager */
$manager = new MongoDB\Manager("mongodb://localhost:27017");


$command = array(
	"createUser" => "USERNAME2",
	"pwd"        => "PASSWORD",
	"roles"      => array(
		array("role" => "clusterAdmin",         "db" => "admin"),
		array("role" => "readWriteAnyDatabase", "db" => "admin"),
		array("role" => "userAdminAnyDatabase", "db" => "admin"),
		"readWrite",
	),
	"writeConcern" => array("w" => "majority"),
);
$createuser = new MongoDB\Command($command);

try {
	$result     = $manager->executeCommand("admin", $createuser);
	$response   = $result->getResponseDocument();
	if ($reponse["ok"]) {
		echo "User created\n";
	}
} catch(Exception $e) {
	echo $e->getMessage(), "\n";
}


?>
```

## Commands and ReadPreferences

```php
<?php

/* Some commands, like count, dbStats, aggregate, ... can be executed on secondaries.
 * Just like with normal queries, an instance of MongoDB\ReadPreference needs to
 * be constructed to prefer certain servers over others */
$prefer = MongoDB\ReadPreference::RP_SECONDARY_PREFERRED;
$tags = array(
	/* Prefer the West Coast datacenter in Iceland */
	array("country" => "iceland", "datacenter" => "west"),

	/* Fallback to any datacenter in Iceland */
	array("country" => "iceland"),

	/* If Iceland is offline, read from whatever is online! */
	array(),
);

/* Construct the ReadPreference object from our options */
$rp = new MongoDB\ReadPreference($prefer, $tags);

/* Construct the MongoDB Manager */
$manager = new MongoDB\Manager("mongodb://localhost:27017");

$query    = array("citizen" => "Iceland");
$count    = new MongoDB\Command(array("count" => "collection", "query" => $query));
$retval   = $manager->executeCommand("db", $count, $rp);
$response = $retval->getResponseDocument();
if ($response["ok"]) {
	printf("db.collection has %d documents matching: %s\n",
		$response["n"],
		BSON\toJSON(BSON\fromArray($query))
	);
}

?>
```
