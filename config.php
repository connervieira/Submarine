<?php
$submarine_config_database_filepath = "./config.json";

if (!file_exists($submarine_config_database_filepath)) { // Check to see if the configuration file needs to be created.
    $submarine_config = array();
    $submarine_config["auth"]["provider"] = "../dropauth/authentication.php";
    $submarine_config["auth"]["admin"] = "";
    $submarine_config["auth"]["authorized_users"] = array();
    $submarine_config["branding"]["name"] = "Submarine";
    $submarine_config["targets"]["main"] = array();

    if (is_writable(dirname($submarine_config_database_filepath))) {
        file_put_contents($submarine_config_database_filepath, json_encode($submarine_config, (JSON_UNESCAPED_SLASHES)));
    } else {
        echo "<p class='red'>Error: The " . realpath(dirname($submarine_config_database_filepath)) . " directory is not writable.</p>";
    }
}

$submarine_config = json_decode(file_get_contents($submarine_config_database_filepath), true);

?>
