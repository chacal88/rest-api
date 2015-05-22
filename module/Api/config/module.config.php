<?php
return array(
    'controllers' => array( //lista os dois controllers do modulo
        'invokables' => array(
            'rest' => 'Api\Controller\RestController',
        )
    ),
    'router' => array( //rotas dos controllers
        'routes' => array(
            'restful' => array(
                'type'    => 'Segment',
                'options' => array(
                    'route'       => '/api/v1/:module[.:entity][.:formatter][/:id]',
                    'constraints' => array(
                        'module' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'entity' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'formatter'  => '[a-zA-Z]+',
                        'id'         => '[a-zA-Z0-9_-]*'
                    ),
                    'defaults' => array(
                        'controller' => 'rest',
                    ),
                ),
            ),
        ),
    ),
);