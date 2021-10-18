<?php

namespace Matt\SyGridBundle\Grid;

class GridRenderer
{
    //TODO: Přidat možnost searche
    //TODO: Přidat lokalizaci
    private \Twig\Environment $environment;
    private ?string $id = null;
    private ?string $serverUrl = null;
    private bool $paging = true;
    private int $limit = 20;
    private ?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager = null;
    private ?string $customStyle = null;
    private \Matt\SyGridBundle\Grid\Cache\GridCaching $cacheManager;

    public function __construct()
    {
        $this->cacheManager = new \Matt\SyGridBundle\Grid\Cache\GridCaching();
    }

    /**
     * @param \Twig\Environment $environment
     */
    public function setEnvironment(\Twig\Environment $environment): void
    {
        $this->environment = $environment;
    }

    /**
     * @param string|null $id
     */
    public function setId(?string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string|null $serverUrl
     */
    public function setServerUrl(?string $serverUrl): void
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     * @param bool $paging
     */
    public function setPaging(bool $paging): void
    {
        $this->paging = $paging;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param string $customStyle
     */
    public function setCustomStyle(string $customStyle): void
    {
        $this->customStyle = $customStyle;
    }

    /**
     * @param string|null $title
     * @param string $key
     * @param callable|null $dataGetter
     * @return Column\GridColumn
     * Adds a single column into sourceManagers column list
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function addColumn(?string $title, string $key, ?callable $dataGetter): \Matt\SyGridBundle\Grid\Column\GridColumn
    {
        $column = new \Matt\SyGridBundle\Grid\Column\GridColumn();
        $column->setTitle($title);
        $column->setKey($key);

        if ($dataGetter !== null) {
            $column->setDataGetter(true);
            $this->cacheManager->cacheColumnDataGetter(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass) . "." . $key, $dataGetter);

            dump($this->cacheManager->cacheColumnDataGetter(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass) . "." . $key));
        }

        $this->sourceManager->columns[] = $column;
        return $column;
    }

    /**
     * @param string $title
     * @param string $key
     * @param Formatter\GridFormatter $formatter
     * @return Column\GridAction
     * Adds a single action into sourceManagers action list
     */
    public function addAction(string $title, string $key, Formatter\GridFormatter $formatter): \Matt\SyGridBundle\Grid\Column\GridAction
    {
        $action = new \Matt\SyGridBundle\Grid\Column\GridAction();
        $action->setTitle($title);
        $action->setKey($key);
        $action->setFormatter($formatter);

        $this->sourceManager->actions[] = $action;
        return $action;
    }

    /**
     * @param ?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager
     */
    public function setSourceManager(?\Matt\SyGridBundle\Grid\Source\AbstractGridSourceManager $sourceManager)
    {
        $this->sourceManager = $sourceManager;
    }

    /**
     * @return string|null
     */
    public function getServerUrl(): ?string
    {
        //TODO: Remove the hardcoded sourceType and add a custom sourceType depending on the users choice, however, we do not have more choices yet.
        $escapedSourceClass = \Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass);
        return $this->serverUrl ?? "/sygrid/data/get/source={$escapedSourceClass}&sourceType=Entity";
    }

    /**
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     * Renders and returns the main of the grid, including styles, data and loaders.
     */
    public function render(): string
    {
        $this->cacheManager->cacheColumns(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass),$this->sourceManager->columns);

        dump($this->cacheManager->cacheColumns(\Matt\SyGridBundle\Grid\Utils\GridHelper::escapeSourceClass($this->sourceManager->sourceClass),$this->sourceManager->columns));

        return $this->environment->render('@SyGrid/Grid/grid.html.twig',
            [
                'serverUrl' => $this->serverUrl ?? $this->getServerUrl(),
                'gridId' => $this->id ?? 'SyGrid',
                'paging' => $this->paging,
                'limit' => $this->limit,
                'columns' => $this->sourceManager->columns,
                'actions' => $this->sourceManager->actions,
                'customStyle' => $this->customStyle
            ]);
    }
}