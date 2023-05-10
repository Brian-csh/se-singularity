<?php
if (is_file(__DIR__ . '/../autoload.php')) {
    require_once __DIR__ . '/../autoload.php';
}
if (is_file(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

use OSS\OssClient;
use OSS\Core\OssException;

$accessKeyId = "LTAI5tDfJuvSDzrYC1uaj1ZG";
$accessKeySecret = "PtdB0rIEvtJslwRhblLtDQmvqnDMYC";
// Endpoint以杭州为例，其它Region请按实际情况填写。
$endpoint = "http://oss-cn-beijing.aliyuncs.com";
// 填写Bucket名称，例如examplebucket。
$bucket= "singularity-eam";

try {
    $ossClient = new OssClient($accessKeyId, $accessKeySecret, $endpoint);
} catch (OssException $e) {
    echo "OSS client initialization failed: " . $e->getMessage();
}