<?php

use App\Entity\Task\TaskRepository;
use App\Entity\User\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Infrastructure\Paginator\SlidingPaginationSubscriberAdapter;
use Infrastructure\Security\Authenticator;
use Infrastructure\Security\Security;
use Infrastructure\Security\SecurityInterface;
use Infrastructure\Security\UserProvider;
use Infrastructure\Services\Flusher;
use Infrastructure\Services\User\PasswordHasher;
use Infrastructure\Services\User\PasswordHasherInterface;
use Infrastructure\Translator;
use Infrastructure\UrlGenerator;
use Knp\Component\Pager\Event\Subscriber\Paginate\PaginationSubscriber;
use Knp\Component\Pager\Event\Subscriber\Sortable\SortableSubscriber;
use Knp\Component\Pager\PaginatorInterface;
use Laminas\Diactoros\ServerRequestFactory;
use League\Route\Router;
use Psr\Container\ContainerInterface;
use App\ReadModel;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\FormRendererInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

return [
    'dependencies' => [
        'factories' => [
            ServerRequestInterface::class => static function (ContainerInterface $container){
                return ServerRequestFactory::fromGlobals();
            },
            SessionInterface::class => static function (ContainerInterface $container) {
                $session = new Session();
                $session->start();
                return $session;
            },
            Router::class => static function (ContainerInterface $container) {
                return new Router();
            },
            ValidatorInterface::class => static function (ContainerInterface $container) {
                return Validation::createValidatorBuilder()
                    ->enableAnnotationMapping()
                    ->getValidator();
            },
            EventDispatcherInterface::class => static function (ContainerInterface $container) {
                $dispatcher = new EventDispatcher();

                $knpDefaults = $container->get('config')['knp']['default'];
                $slidingPaginationS = new SlidingPaginationSubscriberAdapter($knpDefaults);

                $slidingPaginationS->onKernelRequest($container->get(ServerRequestInterface::class));

                $dispatcher->addSubscriber(new PaginationSubscriber());
                $dispatcher->addSubscriber($slidingPaginationS);
                $dispatcher->addSubscriber(new SortableSubscriber());
                return $dispatcher;
            },
            ValidatorExtension::class => static function (ContainerInterface $container) {
                return new ValidatorExtension($container->get(ValidatorInterface::class), true, $container->get(FormRendererInterface::class));
            },
            SecurityInterface::class => static function (ContainerInterface $container) {
                return Security::getInstance($container->get(SessionInterface::class), $container->get(UserProvider::class));
            },
            UserProvider::class => static function (ContainerInterface $container) {
                return new UserProvider($container->get(UserRepository::class), $container->get(PasswordHasherInterface::class));
            },
            Authenticator::class => static function (ContainerInterface $container) {
                return new Authenticator($container->get(UserRepository::class), $container->get(PasswordHasherInterface::class));
            },
            UrlGeneratorInterface::class => static function (ContainerInterface $container) {
                return new UrlGenerator($container->get(Router::class));
            },
            TranslatorInterface::class => static function (ContainerInterface $container) {
                return new Translator();
            },
            Flusher::class => static function (ContainerInterface $container) {
                return new Flusher($container->get(EntityManagerInterface::class));
            },
            PasswordHasherInterface::class => static function (ContainerInterface $container) {
                return new PasswordHasher();
            },
            TaskRepository::class => static function (ContainerInterface $container) {
                return new TaskRepository($container->get(EntityManagerInterface::class));
            },
            UserRepository::class => static function (ContainerInterface $container) {
                return new UserRepository($container->get(EntityManagerInterface::class));
            },
            App\Controller\Task\TaskController::class => static function (ContainerInterface $container) {
                return (new App\Controller\Task\TaskController(
                    $container->get(ReadModel\Task\TaskFetcher::class),
                ))->init($container);
            },
            App\Controller\Auth\LoginController::class => static function (ContainerInterface $container) {
                return (new App\Controller\Auth\LoginController($container->get(Authenticator::class)))->init($container);
            },
            App\UseCase\Tasks\Create\Handler::class => static function (ContainerInterface $container) {
                return new App\UseCase\Tasks\Create\Handler($container->get(EntityManagerInterface::class), $container->get(Flusher::class));
            },
            App\UseCase\Tasks\Edit\Handler::class => static function (ContainerInterface $container) {
                return new App\UseCase\Tasks\Edit\Handler(
                    $container->get(TaskRepository::class),
                    $container->get(EntityManagerInterface::class),
                    $container->get(Flusher::class));
            },
            App\UseCase\Tasks\Complete\Handler::class => static function (ContainerInterface $container) {
                return new App\UseCase\Tasks\Complete\Handler(
                    $container->get(TaskRepository::class),
                    $container->get(EntityManagerInterface::class),
                    $container->get(Flusher::class));
            },
            App\UseCase\User\Create\Handler::class => static function (ContainerInterface $container) {
                return new App\UseCase\User\Create\Handler(
                    $container->get(UserRepository::class),
                    $container->get(EntityManagerInterface::class),
                    $container->get(Flusher::class),
                    $container->get(PasswordHasherInterface::class)
                );
            },
            ReadModel\Task\TaskFetcher::class => static function (ContainerInterface $container) {
                return new ReadModel\Task\TaskFetcher($container->get(Connection::class), $container->get(PaginatorInterface::class));
            }
        ],
    ],

    'app' => getenv('APP_ENV'),
    'debug' => getenv('DEBUG'),
    'app_url' => getenv('APP_URL'),
];