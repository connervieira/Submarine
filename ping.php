<?php
include "./config.php";
if (isset($submarine_config["auth"]["provider"])) {
    $force_login_redirect = true;
    include $submarine_config["auth"]["provider"];
} else {
    echo "<p>There is no authentication provider configured.</p>";
    exit();
}

function online($ip) {
    $pingresult = exec("ping -c 1 -W 1 $ip", $outcome, $status);
    if (0 == $status) {
        return true;
    } else {
        return false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./styles/main.css">
    </head>
    <body>
        <?php
        if (in_array($username, $submarine_config["auth"]["authorized_users"]) == false and $username !== $submarine_config["auth"]["admin"]) { // Check to see if this user is not authorized to view this page.
            echo "<p>You are not permitted to view this page. Please make sure you are signed in with the correct account.</p>";
            exit();
        }


        if (sizeof($submarine_config["targets"]["main"]) > 0) { // Check to see if there is 1 or more targets in the configuration.
            foreach ($submarine_config["targets"]["main"] as $key => $host) {
                if (online($host["ip"])) {
                    echo "<p title='" . $host["ip"] . "'>" . $key . " is <span class='green'>online</span></p>";
                } else {
                    echo "<p title='" . $host["ip"] . "'>" . $key . " is <span class='red'>offline</span></p>";
                }
            }
        } else {
            echo "<p><i>There are no targets configured.</i></p>";
        }
        ?>
    </body>
</html>
