<?php

namespace Caps\LaravelApi\Controllers;

use Caps\LaravelApi\Services\ApiDescService;
use Caps\LaravelApi\Services\ApiListService;

class ApiController
{

    /**
     * 接口列表/详情
     * @throws \ReflectionException
     */
    public function store()
    {
        /**
         * tpl模版路径
         */
        $dir = __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Tpl' . DIRECTORY_SEPARATOR;

        $projectName = config('generate_api.project_name');

        if (!empty(request()->get('detail'))) {
            $apiDesc = new ApiDescService($projectName);
            $apiDesc->render($dir . 'api_desc_tpl.php');
        } else {
            $apiList = new ApiListService($projectName);
            $apiList->render($dir . 'api_list_tpl.php');
        }
    }
}
