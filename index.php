<?php

include_once realpath(dirname(__FILE__))."/lib/Configuration.php";
include_once realpath(dirname(__FILE__))."/lib/XMLObject.php";
include_once realpath(dirname(__FILE__))."/lib/MediathekDL.php";

$config = new Configuration();
$config->setScriptPath(realpath(dirname(__FILE__)));
$config->setFilesPath("./data");
$config->setFeedArgs($argv);

$dl = new MediathekDL($config);
$dl->run();



