#  使用方法
```
根目录下运行： composer require kevinwan/aliyunlog
```

## 代码
```
require __DIR__ . '/vendor/autoload.php';
use kevinwan\aliyunlog;

$data = array(
	'endpoint' => ENDPOINT,
	'accessKeyId' => ACCESSKEYID,
	'accessKey' => ACCESSKEY,
	'logProjectName' => LOG_PROJECT_NAME,
	'logStore' => LOGSTORE_NAME,
);
$log = new aliyunlog\log($data);
$re = $log->write(array('key' => 1));
print_r($re);

```

注：配置常量需自己定义

