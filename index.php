<?php
include "./config.php";
if (isset($submarine_config["auth"]["provider"])) {
    $force_login_redirect = true;
    include $submarine_config["auth"]["provider"];
} else {
    echo "<p>There is no authentication provider configured.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title><?php echo htmlspecialchars(strval($submarine_config["interface"]["branding"]["name"])); ?></title>
        <link rel="stylesheet" type="text/css" href="./styles/main.css">
        <link rel="stylesheet" type="text/css" href="./styles/themes/<?php echo $submarine_config["interface"]["theme"]; ?>.css">
    </head>

    <body>
        <div class="navbar">
            <a class="button" href="./configure.php">Configure</a>
        </div>
        <h1><?php echo htmlspecialchars($submarine_config["interface"]["branding"]["name"]); ?></h1>
        <?php
        if (isset($submarine_config) == false or $submarine_config["auth"]["admin"] == "") {
            echo "<p>There is no administrator configured on this instance. The configuration interface is currently unrestricted to allow for an administrator to be set.</p>";
            echo "<p>The rest of this page will not load until an administrator is configured.</p>";
            exit();
        }

        if (in_array($username, $submarine_config["auth"]["authorized_users"]) == false and $username !== $submarine_config["auth"]["admin"]) { // Check to see if this user is not authorized to view this page.
            echo "<p>You are not permitted to view this page. Please make sure you are signed in with the correct account.</p>";
            exit();
        }
        ?>
        <iframe src="./ping.php"></iframe>
    </body>
</html>
