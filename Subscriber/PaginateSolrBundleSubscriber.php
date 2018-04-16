<?php declare(strict_types=1);

/**
 * KNP Paginator Extra Bundle
 * https://github.com/Martin1982/KnpPaginatorExtraBundle
 */
namespace Martin1982\KnpPaginatorExtraBundle\Subscriber;

use FS\SolrBundle\Doctrine\Mapper\EntityMapperInterface;
use FS\SolrBundle\Query\SolrQuery;
use Knp\Component\Pager\Event\ItemsEvent;
use Solarium\Client;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class PaginateSolrBundleSubscriber
 */
class PaginateSolrBundleSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityMapperInterface
     */
    protected $entityMapper;

    /**
     * PaginateSolrBundleSubscriber constructor
     *
     * @param EntityMapperInterface $entityMapper
     */
    public function __construct(EntityMapperInterface $entityMapper)
    {
        $this->entityMapper = $entityMapper;
    }

    /**
     * @param ItemsEvent $event
     *
     * @throws \FS\SolrBundle\Doctrine\Mapper\SolrMappingException
     */
    public function items(ItemsEvent $event)
    {
        if (\is_array($event->target) && 2 === \count($event->target)) {
            list($client, $query) = array_values($event->target);

            if ($client instanceof Client && $query instanceof SolrQuery) {
                $query->setStart($event->getOffset())->setRows($event->getLimit());
                $solrResult = $client->select($query);
                $entity = $query->getEntity();
                $entities = [];

                $event->items  = $solrResult->getIterator();
                $event->count  = $solrResult->getNumFound();

                foreach ($solrResult as $document) {
                    $entities[] = $this->entityMapper->toEntity($document, $entity);
                }

                $event->setCustomPaginationParameter('result', $entities);
                $event->stopPropagation();
            }
        }
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [ 'knp_pager.items' => ['items', 1] ];
    }
}
