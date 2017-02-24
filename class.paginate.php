<?php

class Paginate
{
    protected $perPage = 10;
    protected $pageParam = 'page';
    protected $pageUrl = NULL;
    protected $totalRecord = 0;

    private $_currentPage = false;
    private $_totalPage = false;

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

    protected function bootStrap()
    {
        if(!$this->_currentPage) {
            $this->currentPage();
        }

        if(!$this->_totalPage) {
            $this->totalPage();
        }
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
        $get_string = parse_url($this->pageUrl, PHP_URL_QUERY);
        parse_str($get_string, $get_array);

        if(isset($get_array[$this->pageParam])) {
            unset($get_array[$this->pageParam]);
        }

        if(count($get_array) > 0) {
            return '&';
        }

        return '?';
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

}