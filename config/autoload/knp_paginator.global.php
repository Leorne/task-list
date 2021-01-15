<?php

use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    'dependencies' => [
        'factories' => [
            PaginatorInterface::class => static function (ContainerInterface $container) {
                return new Paginator($container->get(EventDispatcherInterface::class));
            },
            Processor::class => static function (ContainerInterface $container) {
                return new Processor(
                    $container->get(UrlGeneratorInterface::class),
                    $container->get(TranslatorInterface::class)
                );
            }
        ]
    ],

    'knp' => [
        'default' => [
            'defaultPaginationTemplate' => 'override/pagination/twitter_bootstrap_v4_pagination.html.twig',
            'defaultSortableTemplate' => 'override/pagination/twitter_bootstrap_v4_font_awesome_sortable_link.html.twig',
            'defaultFiltrationTemplate' => 'override/pagination/twitter_bootstrap_v4_filtration.html.twig',
            'defaultPageRange' => '1',
            'defaultPageLimit' => '10',
        ],
    ],
];