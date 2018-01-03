<?php 

class MMSThread extends Thread {

    public $res    = '';
    public $url    = array();
    public $name   = '';
    public $runing = false;
    public $lc     = false;

    public function __construct($url) {

        $this->res    = '';
        $this->param    = 0;
        $this->url   = $url;
    }

    public function run() {
            }

}


