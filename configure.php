<?php
include "./config.php";
if (isset($submarine_config["auth"]["provider"])) {
    $force_login_redirect = true;
    include $submarine_config["auth"]["provider"];
} else {
    echo "<p>There is no authentication provider configured.</p>";
    exit();
}

if ($_POST["interface>theme"] == "dark" or $_POST["interface>theme"] == "light") { $submarine_config["interface"]["theme"] = $_POST["interface>theme"]; } // Update the theme before loading the rest of the page so that the new page theme reflects the changes just made by the user.
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo htmlspecialchars(strval($submarine_config["interface"]["branding"]["name"])); ?> - Configure</title>
        <link rel="stylesheet" type="text/css" href="./styles/main.css">
        <link rel="stylesheet" type="text/css" href="./styles/themes/<?php echo $submarine_config["interface"]["theme"]; ?>.css">
    </head>

    <body>
        <div class="navbar">
            <a class="button" href="./">Back</a>
        </div>
        <h1><?php echo htmlspecialchars($submarine_config["interface"]["branding"]["name"]); ?></h1>
        <h2>Configure</h2>

        <?php

        if ($username !== $submarine_config["auth"]["admin"] and $submarine_config["auth"]["admin"] !== "") { // Check to see if the current user is unauthorized to make configuration changes.
            echo "<p>You do not have permission to configure this instance. Please make sure you are signed in with the correct account.</p>";
            exit();
        }


        if ($_POST["submit"] == "Submit") { // Check to see if the configuration form has been submitted.
            $configuration_valid = true; // This is a placeholder that will be changed to 'false' if an invalid configuration value is encountered.

            if ($_POST["auth>admin"] !== preg_replace("/[^a-zA-Z0-9]/", '', $_POST["auth>admin"])) { // Verify that the admin userinput only contains permitted characters.
                echo "<p class='bad'>The administrator username <b>" . htmlspecialchars($_POST["auth>admin"]) . "</b> contains disallowed characters.</p>";
                $configuration_valid = false;
            }
            $submarine_config["auth"]["admin"] = $_POST["auth>admin"];
            

            $submarine_config["auth"]["authorized_users"] = array();
            if (strlen($_POST["auth>authorized_users"]) > 0) {
                foreach (explode(",", $_POST["auth>authorized_users"]) as $authorized_user) {
                    if (strlen($authorized_user) > 0) {
                        $authorized_user = trim($authorized_user); // Trim any trailing or leading whitespace from this entry.
                        if (trim($authorized_user) == preg_replace("/[^a-zA-Z0-9]/", '', trim($authorized_user))) { // Verify that this entry only contains permitted characters.
                            echo "<p class='bad'>The <b>" . htmlspecialchars($authorized_user) . "</b> username contains disallowed characters.</p>";
                            $configuration_valid = false;
                        } else {
                            array_push($submarine_config["auth"]["authorized_users"], trim(preg_replace("/[^a-zA-Z0-9]/", '', $authorized_user)));
                        }
                    }
                }
            }


            if ($_POST["interface>show_ip"] == "on") { $submarine_config["interface"]["show_ip"] = true;
            } else { $submarine_config["interface"]["show_ip"] = false; }

            if ($_POST["interface>theme"] == "dark" or $_POST["interface>theme"] == "light") {
                $submarine_config["interface"]["theme"] = $_POST["interface>theme"];
            } else {
                echo "<p class='bad'>The interface theme is set to an invalid value.</p>";
                $configuration_valid = false;
            }


            // Add the targets set by the user to the configuration.
            $original_target_count = sizeof($submarine_config["targets"]["main"]); // This variables holds the number of targets in the configuration database before processing the new ones.
            $submarine_config["targets"]["main"] = array(); // Reset the array of targets.
            $target_count = 0; // This is a placeholder that will keep track of each target sequentially.
            for ($i =0; $i <= $original_target_count + 1; $i++) { // Run once for each target in the configuration, plus one to account for the new entry.
                $target_title = $_POST["targets>main>" . strval($target_count) . ">title"];
                if (strlen($target_title) > 0) {
                    if ($target_title !== preg_replace("/[^a-zA-Z0-9'\- ]/", '', $target_title)) { // Check to see if this entry contains disallowed characters.
                        echo "<p class='bad'>The <b>" . htmlspecialchars($target_title) . "</b> target title contains disallowed characters.</p>";
                        $configuration_valid = false;
                    }
                    $submarine_config["targets"]["main"][preg_replace("/[^a-zA-Z0-9'\- ]/", '', $target_title)]["ip"] = $_POST["targets>main>" . strval($target_count) . ">ip"];
                }
                $target_count = $target_count + 1;
            }


            if ($configuration_valid == true) {
                file_put_contents($submarine_config_database_filepath, json_encode($submarine_config, (JSON_UNESCAPED_SLASHES)));
                echo "<p class='good'>Successfully updated configuration.</p>";
            } else {
                echo "<p class='bad'>The configuration was not updated.</p>";
            }
        }
        ?>

        <form method="post">
            <hr><h3>Authentication</h3>
            <label for="auth>admin">Administrator:</label> <input name="auth>admin" id="auth>admin" placeholder="admin" type="text" value="<?php echo $submarine_config["auth"]["admin"]; ?>"><br>
            <label for="auth>authorized_users">Authorized Users:</label> <input name="auth>authorized_users" id="auth>authorized_users" placeholder="user1, user2, user3" type="text" value="<?php $users = ""; foreach ($submarine_config["auth"]["authorized_users"] as $user) { $users = $users . $user . ","; } echo substr($users, 0, strlen($users)-1); ?>"><br>


            <hr><h3>Interface</h3>
            <label for="interface>show_ip">Show Target Addresses:</label> <input name="interface>show_ip" id="interface>show_ip" type="checkbox" <?php if ($submarine_config["interface"]["show_ip"] == true) { echo "checked"; } ?>><br>

            <label for="interface>theme">Theme:</label>
            <select name="interface>theme" id="interface>theme">
                <option name="interface>show_ip" id="interface>show_ip" value="light" <?php if ($submarine_config["interface"]["theme"] == "light") { echo "selected"; } ?>>Light</option>
                <option name="interface>show_ip" id="interface>show_ip" value="dark" <?php if ($submarine_config["interface"]["theme"] == "dark") { echo "selected"; } ?>>Dark</option>
            </select>


            <hr><h3>Targets</h3>
            <?php
            $shown_targets = 0;
            foreach ($submarine_config["targets"]["main"] as $key => $data) {
                echo "<h4>" . $key . "</h4>";
                echo "<label for='targets>main>" . $shown_targets . ">title'>Target:</label> <input name='targets>main>" . $shown_targets . ">title' id='targets>main>" . $shown_targets . ">title' placeholder='Host' type='text' value=\"" . str_replace('"', '\"', $key) . "\"><br>";
                echo "<label for='targets>main>" . $shown_targets . ">ip'>Address:</label> <input name='targets>main>" . $shown_targets . ">ip' id='targets>main>" . $shown_targets . ">ip' placeholder='127.0.0.1' type='text' value=\"" . $data["ip"] . "\"><br><br>";
                $shown_targets = $shown_targets + 1;
            }
            echo "<h4>New Target</h4>";
            echo "<label for='targets>main>" . $shown_targets . ">title'>Target:</label> <input name='targets>main>" . $shown_targets . ">title' id='targets>main>" . $shown_targets . ">title' placeholder='Host' type='text'><br>";
            echo "<label for='targets>main>" . $shown_targets . ">ip'>Address:</label> <input name='targets>main>" . $shown_targets . ">ip' id='targets>main>" . $shown_targets . ">ip' placeholder='127.0.0.1' type='text'><br>";
            ?>
            <hr><input class="button" type="submit" value="Submit" name="submit" id="submit">
        </form>
    </body>
</html>
