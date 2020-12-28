<?php


namespace Caps\LaravelApi\Services;

/**
 * 获取API列表
 * Class ApiList
 * @package App\Helper
 */
class ApiListService extends ApiOnline
{

    /**
     * 跳转到api列表页面
     * @throws \ReflectionException
     */
    public function render($tplPath)
    {
        parent::render($tplPath);
        $dir = base_path('app/Http/Controllers') . DIRECTORY_SEPARATOR;

        $files      = getListDir($dir);
        $filePrefix = 'Controllers/';
        $namespace  = 'App\\Http\\Controllers';
        $excludeApi = ['Controller'];
        $allApiS    = [];
        foreach ($files as $file) {
            $file         = strstr($file, $filePrefix);
            $file         = str_replace(array($filePrefix, '.php'), array('', ''), $file);
            $apiShortName = str_replace(DIRECTORY_SEPARATOR, '_', $file);
            $apiClassName = '\\' . $namespace . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', $file);

            if (!class_exists($apiClassName) || in_array($file, $excludeApi)) {
                continue;
            }

            $ref        = new \ReflectionClass($apiClassName);
            $docComment = $ref->getDocComment();
            $title      = '//请检查接口服务注释' . $apiClassName;
            $desc       = '';
            if ($docComment !== false) {
                $docCommentArr = explode("\n", str_replace('*', '', $docComment));
                $title         = trim($docCommentArr[1]);
                foreach ($docCommentArr as $doc) {
                    $pos = stripos($doc, '@desc');
                    if ($pos !== false) {
                        $desc = substr($doc, $pos + 5);
                    }
                }
            }
            $allApiS['App'][$apiShortName] = [
                'title'   => $title,
                'desc'    => empty($desc) ? $title : $desc,
                'methods' => []
            ];

            $methods = get_class_methods($apiClassName);
            foreach ($methods as $method) {
                if (strpos($method, '__') !== false) {
                    continue;
                }
                $rMethod = new \ReflectionMethod($apiClassName, $method);
                // 排除非公共方法/非当前类定义的方法
                if (!$rMethod->isPublic() || '\\' . $rMethod->getDeclaringClass()->name != $apiClassName) {
                    continue;
                }
                $title      = '//请检查函数注释' . $method;
                $desc       = '';
                $type       = '';
                $docComment = $rMethod->getDocComment();
                if ($docComment !== false) {
                    $docCommentArr = explode("\n", str_replace('*', '', $docComment));
                    $title         = trim($docCommentArr[1]);
                    foreach ($docCommentArr as $doc) {
                        $pos = stripos($doc, '@desc');
                        if ($pos !== false) {
                            $desc = substr($doc, $pos + 5);
                            continue;
                        }
                        $pos = stripos($doc, '@type');
                        if ($pos !== false) {
                            $type = substr($doc, $pos + 5);
                        }
                    }
                }
                $service                                            = $apiShortName . '.' . ucfirst($method);
                $allApiS['App'][$apiShortName]['methods'][$service] = [
                    'service' => $service,
                    'title'   => $title,
                    'desc'    => $desc,
                    'type'    => strtoupper(trim($type))
                ];
            }
        }
        $projectName = $this->projectName;
        $theme       = isset($_GET['type']) ? $_GET['type'] : 'fold';
        include $tplPath;
    }


    function makeApiServiceLink($service, $theme = '')
    {
        $concator = strpos($this->getUri(), '?') ? '&' : '?';
        return $this->getUri() . $concator . 'service=' . $service . '&detail=1' . '&type=' . $theme;
    }

    function getUri()
    {
        return $uri = substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '?'));
    }

    function makeThemeButton($theme)
    {
        if ($theme == 'fold') {
            echo '<div style="float: right"><a href="' . '/api/docs' . '?type=expand">切换到展开版</a></div>';
        } else {
            echo '<div style="float: right"><a href="' . '/api/docs' . '?type=fold">切换到折叠版</a></div>';
        }
    }
}

/**
 * 扫描Http目录，获取所有类
 * @param $dir
 * @return array
 */
function getListDir($dir)
{
    $dirInfo = [];
    foreach (glob($dir . '*') as $v) {
        if (is_dir($v)) {
            $dirInfo = array_merge($dirInfo, getListDir($v . DIRECTORY_SEPARATOR));
        } else {
            $dirInfo[] = $v;
        }
    }
    return $dirInfo;
}
