<?php
    namespace Upload;

    use Zend\Router\Http\Segment;
    use Zend\Router\Http\Literal;
    use Zend\ServiceManager\Factory\InvokableFactory;
    use Doctrine\ORM\Mapping\Driver\AnnotationDriver;

    return [
        'controllers' => [
                'factories' => [
                    Controller\UploadController::class => Controller\Factory\UploadControllerFactory::class
                ]
        ],

        'router' => [
            'routes' => [
                'home' => [
                    'type' => Literal::class,
                    'options' => [
                        'route'    => '/',
                        'defaults' => [
                            'controller' => Controller\UploadController::class,
                            'action'     => 'upload',
                        ],
                    ],
                ],
                'upload' => [
                    'type'    => Segment::class,
                    'options' => [
                        'route'    => '/:action[/:id]',
                        'constraints' => [
                            'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            'id'     => '[0-9]+',
                        ],

                        'defaults' => [
                            'controller' => Controller\UploadController::class,
                            'action'     => 'upload',
                        ],
                    ],
                ],
                'list' => [
                    'type'    => Literal::class,
                    'options' => [
                        'route'    => '/list',
                        'defaults' => [
                            'controller' => Controller\UploadController::class,
                            'action'     => 'list',
                        ],
                    ],
                ],
            ],
        ],

        'view_manager' => [
            'display_not_found_reason' => true,
            'display_exceptions'       => true,
            'doctype'                  => 'HTML5',
            'not_found_template'       => 'error/404',
            'exception_template'       => 'error/index',
            'template_map' => [
                'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
                'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
                'error/404'               => __DIR__ . '/../view/error/404.phtml',
                'error/index'             => __DIR__ . '/../view/error/index.phtml',
            ],
            'template_path_stack' => [
                __DIR__ . '/../view',
            ],
            'strategies' => [
                'ViewJsonStrategy',
            ],
        ],

        'doctrine' => [
            'driver' => [
                __NAMESPACE__ . '_driver' => [
                    'class' => AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => [__DIR__ . '/../src/Entity']
                ],
                'orm_default' => [
                    'drivers' => [
                        __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                    ]
                ]
            ]
        ],
    ];