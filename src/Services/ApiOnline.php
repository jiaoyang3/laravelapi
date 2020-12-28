<?php


namespace Caps\LaravelApi\Services;


class ApiOnline {

    protected $projectName;

    public function __construct($projectName) {
        $this->projectName = $projectName;
    }

    /**
     * @param string $tplPath 模板绝对路径
     */
    public function render($tplPath) {
        header('Content-Type:text/html;charset=utf-8');
    }
}
