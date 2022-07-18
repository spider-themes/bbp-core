<?php

if (!defined('ABSPATH')) { exit; }

class gdbbx_core_info {
    public $code = 'gd-bbpress-toolbox';

    public $version = '6.7.2';
    public $build = 1072;
    public $updated = '2022.02.24';
    public $status = 'stable';
    public $edition = 'pro';
    public $url = 'https://plugins.dev4press.com/gd-bbpress-toolbox/';
    public $author_name = 'Milan Petrovic';
    public $author_url = 'https://www.dev4press.com/';
    public $released = '2012.05.27';

    public $php = '7.2';
    public $mysql = '5.1';
    public $wordpress = '5.3';
    public $bbpress = '2.6.2';

    public $install = false;
    public $update = false;
    public $previous = 0;

    function __construct() { }

    public function to_array() : array {
        return (array)$this;
    }
}
