<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

        //Load Redis cache driver as default and file cache driver as backup
        $this->load->driver('cache', array('adapter' => 'redis','backup' => 'file'));

        //Check if Redis is supported in the current environment and loads fallback page if not supported
        if(!$this->cache->redis->is_supported()){
            echo $this->load->view('fallback', '', true);
            exit();
        }

    }

    //Index method to handle application homepage
    public function index()
	{
        $this->cache->delete('todoData');
        //Loads application homepage view
        //$this->load->view('home');
	}

}
