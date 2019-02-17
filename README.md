API Web-Application
===================

Requirements
------------
- php 5.6
- mysql 5.6
- composer

Installation
------------

### Using Composer

    php composer.phar self-update
    php composer.phar install

(The `self-update` directive is to ensure you have an up-to-date `composer.phar`
available.)

### Local configuration

We need to configure the single componets of the app locally. For this reason copy and rename all files from
    /webapp/config/autoload/*.local.php.dist
to
    /webapp/config/autoload/*.local.php

Now you can configure the application in the newly created files.
(The doctrineconnection.local.php is important for the next step, it configures the database connection).

### Database Migration
@see http://www.fadoe.de/blog/archives/402-Migration-Scripte-mit-Doctrine-erstellen-Beispiel-am-ZendFramework2.html

    //windows
    vendor\bin\doctrine-module.bat migration:<status><migrate><diff><...>
    //linux
    vendor\bin\doctrine-module migration:<status><migrate><diff><...>

Web Server Setup
----------------

### PHP CLI Server

The simplest way to get started if you are using PHP 5.4 or above is to start the internal PHP cli-server in the root directory:

    php -S 0.0.0.0:8080 -t public/ public/index.php

This will start the cli-server on port 8080, and bind it to all network
interfaces.

**Note** The built-in CLI server is *for development only*.

### Apache Setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:

    <VirtualHost *:80>
        ServerName zf2-tutorial.localhost
        DocumentRoot /path/to/zf2-tutorial/public
        SetEnv APPLICATION_ENV "development"
        <Directory /path/to/zf2-tutorial/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
            Require all granted
        </Directory>
    </VirtualHost>
