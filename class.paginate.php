<?php

/**
 * Class Paginate
 *
 * @author me@habibhadi.com
 */


class Paginate
{
    protected $perPage = 10;
    protected $pageParam = 'page';
    protected $pageUrl = NULL;
    protected $totalRecord = 0;

    private $_currentPage = false;
    private $_totalPage = false;
    private $_urlGlue = false;

    /**
     * @param array $settings
     */
    public function __construct($settings = [])
    {
        if (isset($settings['per_page'])) {
            $this->perPage = $settings['per_page'];
        }

        if (isset($settings['page_param'])) {
            $this->pageParam = $settings['page_param'];
        }

        if (isset($settings['page_url'])) {
            $this->pageUrl = $settings['page_url'];
        }

        if (isset($settings['total_record'])) {
            $this->totalRecord = $settings['total_record'];
        }
    }


    /**
     * Bootstrap functions
     *
     * @return $this
     */
    protected function bootStrap()
    {
        if(!$this->_currentPage) {
            $this->currentPage();
        }

        if(!$this->_totalPage) {
            $this->totalPage();
        }

        return $this;
    }

    /**
     * Last limit query string
     *
     * @return string
     */
    public function limit()
    {
        $this->bootStrap();

        $startAt = 0;
        if($this->_currentPage > 1) {
            $startAt = ($this->_currentPage - 1) * $this->perPage;
        }

        return 'LIMIT '.$startAt.','.$this->perPage;
    }


    /**
     * Url glue
     *
     * @return string
     */
    protected function urlGlue()
    {
        if($this->_urlGlue) {
            return $this->_urlGlue;
        }

        $get_string = parse_url($this->pageUrl, PHP_URL_QUERY);
        parse_str($get_string, $get_array);

        if(isset($get_array[$this->pageParam])) {
            unset($get_array[$this->pageParam]);
        }

        if(count($get_array) > 0) {
            $this->_urlGlue = '&';
        }

        $this->_urlGlue = '?';

        return $this->_urlGlue;
    }


    /**
     * Next page url
     *
     * @return bool|string
     */
    public function nextPageUrl()
    {
        $this->bootStrap();

        if($this->_currentPage < $this->_totalPage) {
            return $this->pageUrl.$this->urlGlue().$this->pageParam.'='.($this->_currentPage + 1);
        }

        return false;
    }


    /**
     * Previous page url
     *
     * @return bool|string
     */
    public function previousPageUrl()
    {
        $this->bootStrap();

        if($this->_currentPage > 1) {
            return $this->pageUrl.$this->urlGlue().$this->pageParam.'='.($this->_currentPage - 1);
        }

        return false;
    }

    /**
     * First page url
     *
     * @return string
     */
    public function firstPageUrl()
    {
        $this->bootStrap();

        return $this->pageUrl.$this->urlGlue().$this->pageParam.'=1';
    }


    /**
     * Last page url
     *
     * @return string
     */
    public function lastPageUrl()
    {
        $this->bootStrap();

        return $this->pageUrl.$this->urlGlue().$this->pageParam.'='.$this->_totalPage;
    }


    /**
     * Total record
     *
     * @return int
     */
    public function totalRecord()
    {
        return $this->totalRecord;
    }


    /**
     * Current page number
     *
     * @return int
     */
    public function currentPage()
    {
        if( isset($_GET[$this->pageParam]) ) {
            $this->_currentPage = intval($_GET[$this->pageParam]);
        }
        else {
            $this->_currentPage = 1;
        }

        return $this->_currentPage;
    }

    /**
     * Total page
     *
     * @return float
     */
    public function totalPage()
    {
        $this->_totalPage = ceil($this->totalRecord() / $this->perPage);

        return $this->_totalPage;
    }


    /**
     * Page numbers
     *
     * @param bool|false $excludeFirstLast
     * @return array
     */
    public function pages($excludeFirstLast = false)
    {
        $pageArray = [];
        $pageDiff = 2;
        $totalPageShow = 5;

        $loopStartAt = $this->_currentPage - $pageDiff;
        $loopEndsAt = $this->_currentPage + $pageDiff;

        if($loopStartAt < 1) {
            $loopStartAt = 1;
        }

        if($loopEndsAt > $this->_totalPage) {
            $loopEndsAt = $this->_totalPage;
        }

        if(($loopEndsAt - $loopStartAt) < ($totalPageShow - 1)) {
            $loopEndsAt = $loopEndsAt + ($loopEndsAt - $loopStartAt);

            if($loopEndsAt - $loopStartAt > $totalPageShow) {
                $loopEndsAt = $loopEndsAt + ($totalPageShow - 1) - ($loopEndsAt - $loopStartAt);
            }

            if($loopEndsAt > $this->_totalPage) {
                $loopEndsAt = $this->_totalPage;
            }

            if(($loopEndsAt - $loopStartAt) < ($totalPageShow - 1)) {
                $loopStartAt = $loopStartAt - ($loopEndsAt - $loopStartAt);
            }

            if($loopStartAt < 1) {
                $loopStartAt = 1;
            }
        }


        if(!$excludeFirstLast && $loopStartAt > 1) {
            $pageArray[] = [
                'number' => 1,
                'url' => $this->pageUrl.$this->urlGlue().$this->pageParam.'=1',
            ];

            $pageArray[] = [
                'number' => false,
                'url' => false,
            ];
        }

        for($i = $loopStartAt; $i <= $loopEndsAt; $i++) {
            $pageArray[] = [
                'number' => $i,
                'url' => $this->pageUrl.$this->urlGlue().$this->pageParam.'='.$i,
            ];
        }

        if(!$excludeFirstLast && $loopEndsAt < $this->_totalPage) {
            $pageArray[] = [
                'number' => false,
                'url' => false,
            ];

            $pageArray[] = [
                'number' => $this->_totalPage,
                'url' => $this->pageUrl.$this->urlGlue().$this->pageParam.'='.$this->_totalPage,
            ];
        }

        return $pageArray;
    }

}