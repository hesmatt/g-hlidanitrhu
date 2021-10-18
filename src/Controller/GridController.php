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

    public function getAction(\Symfony\Component\HttpFoundation\Request $request, string $source, string $sourceType, ?int $limit, ?int $offset): \Symfony\Component\HttpFoundation\Response
    {
        if ($sourceType === 'Entity') {
            $gridSource = new \Matt\SyGridBundle\Grid\Source\EntityGridSourceManager();
            $gridSource->setEntityManager($this->entityManager);
            $gridSource->setSource($source);
            $gridSource->setLimit($limit);
            $gridSource->setOffset($offset);

            if (($columns = $this->cacheManager->cacheColumns(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($source))) !== null) {
                $gridSource->setColumns($columns);
            }

            $results = $gridSource->getData();
            foreach ($gridSource->columns as $column) {
                if (!$column->isReflected()) {
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
            return new \Symfony\Component\HttpFoundation\Response('Non existing source type', 403);;
        }

        return new \Symfony\Component\HttpFoundation\Response();
    }

}