<?php

namespace Ycore\Dao;

class Channel
{


    protected $size = 0;

    protected $page = 0;

    protected $path = '';


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


    public static function channel($size = 10, $page = 1, $path = "/list-[PAGE].html")
    {

        $c = new Channel();

        $c->setSize($size??10);
        $c->setPage($page??1);
        $c->setPath($path);


        return $c;
    }


}
