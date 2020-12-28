# LaravelApi

Automatic generation of laravel API documents

## Version Compatibility

| PHP     | Laravel | 
|:-------:|:-------:|
| >=7.2 | >=5.5    | 

## Documentation

### Install
composer require caps/laravelapi

### add provider
edit app\Providers\AppServiceProvider.php
```
public function boot()
{
    if ($this->app->environment() !== 'production') {
        $this->app->register(LaravelApiProvider::class);
    }
}
```

### edit config file
you can copy `laravelapi/config/generate_api.php` into `config` folder  
definition `project_name` free 

### Usage
The usage is very simple  
Just define @desc before the class  
Just define @rule/@return/@exception before the class

example
```
/**
 * 测试队列使用
 * Class PodcastController
 * @package App\Http\Controllers\API
 */
class PodcastController extends Controller
{
    /**
     * 入队列
     * @decc 入队列
     * @rule string product_id 产品ID required
     * @rule int status 状态 required
     * @rule string company_name 所属企业
     * @rule string category_name 产品类型名称 required
     * @rule string name 产品名称 required
     * @return string product_id 产品ID
     * @return string code 产品编码(系统)
     * @return string status 状态
     * @return string company_name 所属企业
     * @return string category_name 产品类型名称
     * @return string category_code 产品类型编码
     * @return string name 产品名称
     * @return string model 产品编码(企业）
     * @return string bundle_amount 产品装箱规格
     * @return string buyer_company 可见企业
     * @return string remark 产品备注
     * @return \Illuminate\Http\JsonResponse
     */
    public function store()
    {
        return response()->json(['msg' => '队列添加成功']);
    }

    /**
     * 测试
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function test()
    {
        return view('api_list');
    }
}
```

**last you can visit `http://{website}/gapi/docs` to Preview effect**  
**have fun !!! **
                                                 
## Thinks
Thank you very much for `phalapi`, api document inspiration source `phalapi`  
`phalapi` is a very simple and easy to use PHP framework, Its api documentation is so elegant that I want to port it to the laravel framework  
                      
https://www.phalapi.net/
