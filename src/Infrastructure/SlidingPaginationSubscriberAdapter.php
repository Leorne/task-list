<?php

namespace Infrastructure;

use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Knp\Bundle\PaginatorBundle\Subscriber\SlidingPaginationSubscriber as ParentSubscriber;
use Knp\Component\Pager\Event\PaginationEvent;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class SlidingPaginationSubscriberAdapter implements EventSubscriberInterface
{
    private $route;
    private $params = [];
    private $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function onKernelRequest(ServerRequestInterface $request): void
    {
        $this->route = null;
        $this->params = $request->getQueryParams();
    }

    public function pagination(PaginationEvent $event): void
    {
        // default sort field and order
        $eventOptions = $event->options;

        if (isset($eventOptions['defaultSortFieldName']) && !isset($this->params[$eventOptions['sortFieldParameterName']])) {
            $this->params[$eventOptions['sortFieldParameterName']] = $eventOptions['defaultSortFieldName'];
        }

        if (isset($eventOptions['defaultSortDirection']) && !isset($this->params[$eventOptions['sortDirectionParameterName']])) {
            $this->params[$eventOptions['sortDirectionParameterName']] = $eventOptions['defaultSortDirection'];
        }

        // remove default sort params from pagination links
        if (isset($eventOptions['removeDefaultSortParams']) && true === $eventOptions['removeDefaultSortParams']) {
            $defaultSortFieldName = $eventOptions['defaultSortFieldName'];
            $sortFieldParameterName = $this->params[$eventOptions['sortFieldParameterName']];
            $isFieldEqual = $defaultSortFieldName === $sortFieldParameterName;
            $defaultSortDirection = $eventOptions['defaultSortDirection'];
            $sortDirectionParameterName = $this->params[$eventOptions['sortDirectionParameterName']];
            $isDirectionEqual = $defaultSortDirection === $sortDirectionParameterName;

            if (isset($defaultSortFieldName) && isset($sortFieldParameterName) && $isFieldEqual
                && isset($defaultSortDirection) && isset($sortDirectionParameterName) && $isDirectionEqual) {
                unset($this->params[$eventOptions['sortFieldParameterName']]);
                unset($this->params[$eventOptions['sortDirectionParameterName']]);
            }
        }

        $pagination = new SlidingPagination($this->params);

        $pagination->setUsedRoute($this->route);
        $pagination->setTemplate($this->options['defaultPaginationTemplate']);
        $pagination->setSortableTemplate($this->options['defaultSortableTemplate']);
        $pagination->setFiltrationTemplate($this->options['defaultFiltrationTemplate']);
        $pagination->setPageRange($this->options['defaultPageRange']);
        $pagination->setPageLimit($this->options['defaultPageLimit']);

        $event->setPagination($pagination);
        $event->stopPropagation();
    }
    public static function getSubscribedEvents()
    {
        return ParentSubscriber::getSubscribedEvents();
    }
}