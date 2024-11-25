<?php
include "./config.php";
if (isset($submarine_config["auth"]["provider"])) { // Check to see if an authentication provider has been configured.
    $force_login_redirect = true;
    include $submarine_config["auth"]["provider"]; // Load the authentication provider.
} else {
    echo "<p>There is no authentication provider configured.</p>";
    exit();
}

function online($ip) {
    exec("ping -c 1 -W 1 $ip", $outcome, $status); // Run a single ping, with a one second time-out.

    if (0 == $status) { return true;
    } else { return false; }
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <link rel="stylesheet" type="text/css" href="./styles/main.css">
        <link rel="stylesheet" type="text/css" href="./styles/themes/<?php echo $submarine_config["interface"]["theme"]; ?>.css">
    </head>
    <body>
        <?php
        if (in_array($username, $submarine_config["auth"]["authorized_users"]) == false and $username !== $submarine_config["auth"]["admin"]) { // Check to see if this user is not authorized to view this page.
            echo "<p class='bad'>You are not permitted to view this page. Please make sure you are signed in with the correct account.</p>";
            exit();
        }


        if (sizeof($submarine_config["targets"]["main"]) > 0) { // Check to see if there is 1 or more targets in the configuration.
            foreach ($submarine_config["targets"]["main"] as $key => $host) {
                if ($submarine_config["interface"]["show_ip"]) { echo "<p class='address'>" . $host["ip"] . "</p>"; } // Show the IP address of this target if configured to do so.
                if (online($host["ip"])) {
                    echo "<p title='" . $host["ip"] . "'>" . $key . " is <span class='good'>online</span></p>";
                } else {
                    echo "<p title='" . $host["ip"] . "'>" . $key . " is <span class='bad'>offline</span></p>";
                }
            }
        } else {
            echo "<p><i>There are no targets configured.</i></p>";
        }
        ?>
    </body>
</html>
