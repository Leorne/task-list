<?php

use Infrastructure\Security\SecurityInterface;
use Infrastructure\Template\TemplateRenderer;
use Infrastructure\Template\Twig\Extensions\AssetsExtension;
use Infrastructure\Template\Twig\Extensions\FlashExtension;
use Infrastructure\Template\Twig\Extensions\RouteExtension;
use Infrastructure\Template\Twig\Extensions\StatusExtension;
use Infrastructure\Template\Twig\TwigRenderer;
use Knp\Bundle\PaginatorBundle\Helper\Processor;
use Knp\Bundle\PaginatorBundle\Twig\Extension\PaginationExtension;
use League\Route\Router;
use Psr\Container\ContainerInterface;
use Symfony\Bridge\Twig\Extension\FormExtension;
use Symfony\Bridge\Twig\Form\TwigRendererEngine;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRenderer;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\RuntimeLoader\ContainerRuntimeLoader;
use Twig\RuntimeLoader\RuntimeLoaderInterface;

return [
    'dependencies' => [
        'factories' => [
            TemplateRenderer::class => static function (ContainerInterface $container) {
                return new TwigRenderer(
                    $container->get(Environment::class),
                    $container->get('config')['templates']['extension']
                );
            },
            Environment::class => static function (ContainerInterface $container) {
                $debug = $container->get('config')['debug'];
                $config = $container->get('config')['twig'];

                $loader = new FilesystemLoader();
                $loader->addPath($config['template_dir']);

                $environment = new Environment($loader, [
                    'cache' => $debug ? false : $config['cache_dir'],
                    'debug' => $debug,
                    'strict_variables' => $debug,
                    'auto_reload' => $debug,
                ]);

                $environment->addRuntimeLoader($container->get(RuntimeLoaderInterface::class));

                if ($debug) {
                    $environment->addExtension(new DebugExtension());
                }

                foreach ($config['extensions'] as $extension) {
                    $environment->addExtension($container->get($extension));
                }

                $environment->addGlobal('auth', $container->get(SecurityInterface::class));

                return $environment;
            },
            RuntimeLoaderInterface::class => static function (ContainerInterface $container) {
                return new ContainerRuntimeLoader($container);
            },
            FormRendererInterface::class => static function (ContainerInterface $container) {
                return $container->get(FormRenderer::class);
            },
            Symfony\Component\Form\FormRenderer::class => static function (ContainerInterface $container) {
                return new Symfony\Component\Form\FormRenderer($container->get(TwigRendererEngine::class));
            },
            TwigRendererEngine::class => static function (ContainerInterface $container) {
                $formTemplate = $container->get('config')['templates']['form_template'];
                return new TwigRendererEngine([$formTemplate], $container->get(Environment::class));
            },
            FormFactoryInterface::class => static function (ContainerInterface $container) {
                return Forms::createFormFactoryBuilder()->addExtension($container->get(ValidatorExtension::class))->getFormFactory();
            },
            RouteExtension::class => static function (ContainerInterface $container) {
                return new RouteExtension($container->get(UrlGeneratorInterface::class));
            },
            FormExtension::class => static function (ContainerInterface $container) {
                return new FormExtension();
            },
            AssetsExtension::class => static function (ContainerInterface $container) {
                $appUrl = $container->get('config')['app_url'];
                return new AssetsExtension($appUrl);
            },
            FlashExtension::class => static function (ContainerInterface $container) {
                return new FlashExtension($container->get(SessionInterface::class));
            },
            PaginationExtension::class => static function (ContainerInterface $container) {
                return new PaginationExtension($container->get(Processor::class));
            },
            StatusExtension::class => static function(ContainerInterface $container){
                return new StatusExtension();
            },
        ],
    ],

    'templates' => [
        'extension' => '.html.twig',
        'form_template' => 'override/form/bootstrap_4_layout.html.twig'
    ],

    'twig' => [
        'template_dir' => 'templates',
        'cache_dir' => 'var/cache/twig',
        'extensions' => [
            RouteExtension::class,
            FormExtension::class,
            AssetsExtension::class,
            FlashExtension::class,
            PaginationExtension::class,
            StatusExtension::class,
        ],
    ],
];

