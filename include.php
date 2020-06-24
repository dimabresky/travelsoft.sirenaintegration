<?php

$classes = array(
    "travelsoft\\sirenaintegration\\BookManager" => "lib/BookManager.php",
    "travelsoft\\sirenaintegration\\Tools" => "lib/Tools.php",
    "travelsoft\\sirenaintegration\\EventsHandlers" => "lib/EventsHandlers.php",
    "travelsoft\\sirenaintegration\\HighloadblockAdapter" => "lib/HighloadblockAdapter.php",
    "travelsoft\\sirenaintegration\\Option" => "lib/Option.php",
    "travelsoft\\sirenaintegration\\Request" => "lib/Request.php",
    "travelsoft\\sirenaintegration\\Xml" => "lib/Xml.php",
);

CModule::AddAutoloadClasses("travelsoft.sirenaintegration", $classes);
