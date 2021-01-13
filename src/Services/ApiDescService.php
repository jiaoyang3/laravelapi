<?php


namespace Caps\LaravelApi\Services;


/**
 *
 * Class ApiDesc
 * @package App\Helper
 */
class ApiDescService extends ApiOnline
{

    /**
     * 跳转到接口详情页面
     * @param string $tplPath
     * @throws \ReflectionException
     */
    public function render($tplPath)
    {
        parent::render($tplPath);

        $service = request()->get('service');

        if (empty($service)) {
            include $tplPath;
            return;
        }
        list($class, $method) = explode('.', $service);
        $namespace = 'App\\Http\\Controllers';
        $className = '\\' . $namespace . '\\' . str_replace('_', '\\', ucfirst($class));

        $rules         = array();
        $returns       = array();
        $description   = '';
        $descComment   = '';
        $methodComment = '';
        $url           = '';
        $routeComment  = '';//路由信息
        $exceptions    = array();

        $projectName = $this->projectName;

        // 整合需要的类注释
        $rClass              = new \ReflectionClass($className);
        $classDocComment     = $rClass->getDocComment();
        $needClassDocComment = '';
        foreach (explode("\n", $classDocComment) as $comment) {
            if (stripos($comment, '@exception') !== FALSE
                || stripos($comment, '@return') !== FALSE) {
                $needClassDocComment .= "\n" . $comment;
            }
        }

        // 方法注释
        $rMethod       = new \ReflectionMethod($className, $method);
        $docCommentArr = explode("\n", $needClassDocComment . "\n" . $rMethod->getDocComment());

        foreach ($docCommentArr as $comment) {
            $comment = trim($comment);

            //标题描述
            if (empty($description) && strpos($comment, '@') === FALSE && strpos($comment, '/') === FALSE) {
                $description = substr($comment, strpos($comment, '*') + 1);
                continue;
            }

            // 路由信息
            $pos = stripos($comment, '@route');
            if ($pos !== FALSE) {
                $routeComment = substr($comment, $pos + 6);
                continue;
            }

            // 请求类型
            $pos = stripos($comment, '@type');
            if ($pos !== FALSE) {
                $methodComment = strtoupper(trim(substr($comment, $pos + 5)));
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@url');
            if ($pos !== FALSE) {
                $url = substr($comment, $pos + 5);
                continue;
            }

            //@desc注释
            $pos = stripos($comment, '@desc');
            if ($pos !== FALSE) {
                $descComment = substr($comment, $pos + 5);
                continue;
            }

            //@rule注释
            $pos = stripos($comment, '@rule');
            if ($pos !== FALSE) {
                $ruleCommonArr = explode(' ', substr($comment, $pos + 6));
                $rules[]       = [
                    'name'    => @$ruleCommonArr[1],
                    'type'    => @$ruleCommonArr[0],
                    'require' => (@$ruleCommonArr[3] === 'required') ? true : false,
                    'desc'    => @$ruleCommonArr[2],
                ];
                continue;
            }

            //@exception注释
            $pos = stripos($comment, '@exception');
            if ($pos !== FALSE) {
                $exArr                 = explode(' ', trim(substr($comment, $pos + 10)));
                $exceptions[$exArr[0]] = $exArr;
                continue;
            }

            //@return注释
            $pos = stripos($comment, '@return');
            if ($pos === FALSE) {
                continue;
            }

            $returnCommentArr = explode(' ', substr($comment, $pos + 8));
            //将数组中的空值过滤掉，同时将需要展示的值返回
            $returnCommentArr = array_values(array_filter($returnCommentArr));
            if (count($returnCommentArr) < 2) {
                continue;
            }
            if (!isset($returnCommentArr[2])) {
                $returnCommentArr[2] = '';    //可选的字段说明
            } else {
                //兼容处理有空格的注释
                $returnCommentArr[2] = implode(' ', array_slice($returnCommentArr, 2));
            }

            //以返回字段为key，保证覆盖
            $returns[$returnCommentArr[1]] = $returnCommentArr;
        }
        if (empty($routeComment)) {
            $routeComment = $service;
        }

        include $tplPath;
    }
}
