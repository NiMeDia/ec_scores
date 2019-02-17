<?php
/**
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace Application;

use Zend\Router\Http\Literal;
use Zend\Router\Http\Segment;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'doctrine' => array(
        'driver' => array(
            // overriding zfc-user-doctrine-orm's config
            'app_entities' => array(
                'class' => \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                'cache' => 'array',
                'paths' => __DIR__ . '/../src/Application/Entity',
            ),

            'orm_default' => array(
                'drivers' => array(
                    'Application\\Entity' => 'app_entities',
                ),
            ),
        ),
    ),
    'router' => [
        'routes' => [
            'home' => [
                'type' => Literal::class,
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'api' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/api[/:action]',
                    'defaults' => [
                        'controller' => Controller\ApiController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\AbstractEntityBasedControllerFactory::class,
            Controller\ApiController::class => Controller\AbstractEntityBasedControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
//            \Zend\I18n\Translator\TranslatorInterface::class => \Zend\I18n\Translator\TranslatorServiceFactory::class,
        ]
    ],
    'view_helpers' => [
        'invokables' => [
//            'translate' => \Zend\I18n\View\Helper\Translate::class
        ]
    ],
    'view_manager' => [
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
//            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
//            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
//            'error/404'               => __DIR__ . '/../view/error/404.phtml',
//            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ],
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
];