# Documentation

This document explains how to install, set up, and use Submarine.


## Installation

1. Install Apache, or another web-server host.
    - Example: `sudo apt-get install apache2`
2. Install and enable PHP for your web-server.
    - Example: `sudo apt-get install php; sudo a2enmod php*`
3. Restart your web-server host.
    - Example: `sudo apache2ctl restart`
4. Install DropAuth.
    - Submarine uses DropAuth to manage authentication.
    - You can learn more about DropAuth at <https://v0lttech.com/dropauth.php>
5. Install Submarine.
    - After downloading Submarine, move the main directory to the root of your web-server directory.
    - Example: `mv ~/Downloads/submarine /var/www/html/submarine/`


## Setup

### Basic Configuration

1. Make the Submarine directory writable.
    - Example: `chmod 777 /var/www/html/submarine/`
2. Navigate to DropAuth in your web browser.
    - Example: `http://localhost/dropauth/`
3. If you don't already have an account on your DropAuth instance, create one.
4. Log into your DropAuth account.
    - The account you log in with should be the one you plan to use as your Submarine administration account.
5. Navigate to Submarine in your web browser.
    - Example: `http://localhost/submarine/`
6. Press the "Configure" button on the top left of the main Submarine webpage.
7. Under the "Authentication" section, set the "Administrator" field to the username of your DropAuth account.
    - This user will become the administrator user, and the configuration interface will be restricted to all other users.
    - Note that after submitting the updated administrator user, the configuration interface will lock out all other users, and only the specified administrator will be able to configure Submarine.
        - If you enter the wrong username and accidentally lock yourself out of the configuration interface, you can manually modify the the `config.json` file in the Submarine directory to adjust the configuration.
8. Make other configuration changes as desired.

### Setting Targets

"Targets" are network devices that Subarmine will run status checks on. All existing targets are displayed on the Configuration page under the "Targets" section. To edit an existing target, simply change its information and submit the configuration form. To add a new target, use the "New Target" section at the bottom of the list.

Each target has two attributes. The first attribute is a friendly name for the device. This name can be any memorable/recognizable title. The second attribute is the address that Submarine will ping to check the availability of the target. The address can be either a domain name or an IP address.
