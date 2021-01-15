<?php

namespace App\Controller;

use Infrastructure\Security\SecurityInterface;
use Infrastructure\Template\TemplateRenderer;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\Response\RedirectResponse;
use Psr\Container\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

abstract class AbstractController
{
    protected TemplateRenderer $template;
    protected ContainerInterface $container;
    protected SessionInterface $session;
    protected FormFactoryInterface $formFactory;
    protected SecurityInterface $security;


    public function init(ContainerInterface $container): self
    {
        $this->initContainer($container);
        $this->initSession();
        $this->initTemplate();
        $this->initSecurity();
        $this->initFormFactory();
        return $this;
    }

    protected function initContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }

    protected function initSession(): void
    {
        $this->session = $this->container->get(SessionInterface::class);
    }

    protected function initTemplate(): void
    {
        $this->template = $this->container->get(TemplateRenderer::class);
    }

    protected function initSecurity(): void
    {
        $this->security = $this->container->get(SecurityInterface::class);
    }

    protected function initFormFactory(): void
    {
        $this->formFactory = $this->container->get(FormFactoryInterface::class);
    }

    protected function isGet(string $method)
    {
        return strtolower($method) === 'get';
    }

    protected function isPost(string $method)
    {
        return strtolower($method) === 'post';
    }

    protected function render(string $name, array $params = []): HtmlResponse
    {
        return new HtmlResponse($this->template->render($name, $params));
    }

    protected function redirect(string $path): RedirectResponse
    {
        return new RedirectResponse($path);
    }

    /**
     * @param string $type
     * @param array|string $message
     */
    protected function addFlash(string $type, $message): void
    {
        $this->session->getFlashBag()->set($type, $message);
    }

    protected function createForm(string $type, $data = null, array $options = []): FormInterface
    {
        return $this->formFactory->create($type, $data, $options);
    }


}