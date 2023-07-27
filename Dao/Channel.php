<?php

namespace Ycore\Dao;

class Channel
{


    protected $size = 0;

    protected $page = 0;

    protected $path = '';

    protected $orderField = '';

    protected $orderDirection = '';

    /**
     * @return string
     */
    public function getOrderField(): string
    {
        return $this->orderField;
    }

    /**
     * @param string $orderField
     */
    private function setOrderField(string $orderField): void
    {
        $this->orderField = $orderField;
    }

    /**
     * @return string
     */
    public function getOrderDirection(): string
    {
        return $this->orderDirection;
    }

    /**
     * @param string $orderDirection
     */
    private function setOrderDirection(string $orderDirection): void
    {
        $this->orderDirection = $orderDirection;
    }


    public function getSize()
    {
        return $this->size;
    }


    private function setSize($size): void
    {
        $this->size = $size;
    }


    public function getPage()
    {
        return $this->page;
    }


    private function setPage($page): void
    {
        $this->page = $page;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     */
    private function setPath(string $path): void
    {
        $this->path = $path;
    }


    private function __construct()
    {
    }


    public static function channel($size = 10, $page = 1, $path = "/list-[PAGE].html", $orderField = 'push_time', $orderDirection = 'desc')
    {

        $c = new Channel();

        $c->setSize($size ?? 10);
        $c->setPage($page ?? 1);
        $c->setPath($path);
        $c->setOrderField($orderField);
        $c->setOrderDirection($orderDirection);


        return $c;
    }


}
