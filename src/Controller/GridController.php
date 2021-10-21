<?php
declare(strict_types=1);

namespace Matt\SyGridBundle\Controller;

class GridController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    private \Doctrine\ORM\EntityManagerInterface $entityManager;
    private \Matt\SyGridBundle\Grid\Cache\GridCaching $cacheManager;

    public function __construct(\Doctrine\ORM\EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->cacheManager = new \Matt\SyGridBundle\Grid\Cache\GridCaching();
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getAction(\Symfony\Component\HttpFoundation\Request $request): \Symfony\Component\HttpFoundation\Response
    {
        $source = $request->query->get('source', null);
        $sourceType = $request->query->get('sourceType', null);
        $limit = (int)$request->query->get('limit', null);
        $offset = (int)$request->query->get('offset', null);
        $search = $request->query->get('search', null);

        if ($source === null || $sourceType === null) {
            return new \Symfony\Component\HttpFoundation\Response('Missing required parameters', 403);
        }

        if ($sourceType === 'Entity') {
            $gridSource = $this->createSourceManager($sourceType, [
                'entityManager' => $this->entityManager,
                'source' => $source,
                'limit' => $limit,
                'offset' => $offset,
                'search' => $search
            ]);

            $results = $gridSource->getData();
            foreach ($gridSource->columns as $column) {
                if ($column->hasDataGetter()) {
                    foreach ($results as &$result) {
                        $cachedFunction = $this->cacheManager->cacheColumnDataGetter(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($source) . "." . $column->getKey());
                        if ($cachedFunction !== null) {
                            $result[$column->getKey()] = $cachedFunction->getClosure()($this->entityManager, $result);
                        }
                    }
                }
            }
            return $this->json(['results' => $results, 'count' => $gridSource->getCount()]);
        } else {
            return new \Symfony\Component\HttpFoundation\Response('Non existing source type', 403);
        }
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     * @throws \Exception
     * Returns grid source with all parameters pre-filled in
     */
    private function createSourceManager(string $sourceType, array $params): ?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager
    {
        if (!isset($params['source']) || $params['source'] == null) {
            throw new \Exception('Missing source in GridController for source manager creation');
        }

        $gridSource = null;
        $cachedParameters = $this->cacheManager->cacheSourceParameters(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($params['source']));
        $cachedColumns = $this->cacheManager->cacheColumns(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($params['source']));
        if ($sourceType === 'Entity') {
            $gridSource = new \Matt\SyGridBundle\Grid\Source\EntityGridSourceManager($this->entityManager);
            $gridSource->setSource($params['source']);
            $gridSource->setLimit($params['limit']);
            $gridSource->setOffset($params['offset']);
            $gridSource->setWhere($cachedParameters['where']);
            $gridSource->addFields($cachedParameters['fields']);
            if ($params['search'] !== null) {
                $gridSource->setSearch($params['search']);
            }
            if ($cachedColumns !== null) {
                $gridSource->setColumns($cachedColumns);
            }
        }

        return $gridSource;
    }

}