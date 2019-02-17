<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
$dataDir = __DIR__ . '/../../data';
return array(
    'doctrine' => array(
        // migrations configuration
        'migrations_configuration' => array(
            'orm_default' => array(
                'directory' => $dataDir . '/doctrine-migrations',
                'name'      => 'Doctrine Database Migrations',
                'namespace' => 'Application',
                'table'     => 'migrations',
            ),
        ),
    ),
//    'service_manager' => array(
//        'invokables' => array(
//            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
//        ),
//    ),
);