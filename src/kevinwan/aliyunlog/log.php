<?php
/*
 * ░░░░░░░░░░░░░░░░░░░░░░░░▄░░
 * ░░░░░░░░░▐█░░░░░░░░░░░▄▀▒▌░
 * ░░░░░░░░▐▀▒█░░░░░░░░▄▀▒▒▒▐
 * ░░░░░░░▐▄▀▒▒▀▀▀▀▄▄▄▀▒▒▒▒▒▐
 * ░░░░░▄▄▀▒░▒▒▒▒▒▒▒▒▒█▒▒▄█▒▐
 * ░░░▄▀▒▒▒░░░▒▒▒░░░▒▒▒▀██▀▒▌
 * ░░▐▒▒▒▄▄▒▒▒▒░░░▒▒▒▒▒▒▒▀▄▒▒
 * ░░▌░░▌█▀▒▒▒▒▒▄▀█▄▒▒▒▒▒▒▒█▒▐
 * ░▐░░░▒▒▒▒▒▒▒▒▌██▀▒▒░░░▒▒▒▀▄
 * ░▌░▒▄██▄▒▒▒▒▒▒▒▒▒░░░░░░▒▒▒▒
 * ▀▒▀▐▄█▄█▌▄░▀▒▒░░░░░░░░░░▒▒▒
 * 单身狗就这样默默地看着你，一句话也不说。
 * */
namespace kevinwan\aliyunlog;
require __DIR__ . '/../aliyun-log-php-sdk/Log_Autoload.php';

class log {
	protected $endpoint; // 选择与上面步骤创建 project 所属区域匹配的 Endpoint
	protected $accessKeyId; // 使用你的阿里云访问秘钥 AccessKeyId
	protected $accessKey; // 使用你的阿里云访问秘钥 AccessKeySecret
	protected $project; // 上面步骤创建的项目名称
	protected $logstore; // 上面步骤创建的日志库名称
	protected $client;

	public function __construct($param = array()) {
		$this->endpoint = $param['endpoint'];
		$this->accessKeyId = $param['accessKeyId'];
		$this->accessKey = $param['accessKey'];
		$this->project = $param['logProjectName'];
		$this->logstore = $param['logStore'];
		$this->client = new \Aliyun_Log_Client($this->endpoint, $this->accessKeyId, $this->accessKey);
	}

	/**
	 * 写入日志
	 * @param $topic
	 * @param array $param
	 * @return int
	 * @throws \Aliyun_Log_Exception
	 */
	public function write($param) {
		if (!is_array($param) || empty($param)) {
			return 0;
		}

		$list = array();
		foreach ($param as $key => $item) {
			if (!$item) {
				continue;
			}

			$list[$key] = is_array($item) ? json_encode($item) : $item;
		}

//        $topic = "";
		$source = isset($list['source']) ? $list['source'] : '';
		$topic = isset($list['topic']) ? $list['topic'] : '';
		if ($source) {
			unset($list['source']);
		}
		if ($topic) {
			unset($list['topic']);
		}
		$logitems = array();
		$contents = $list;
		try {
			$logItem = new \Aliyun_Log_Models_LogItem();
			$logItem->setTime(time());
			$logItem->setContents($contents);
			$logitems[] = $logItem;
			$req2 = new \Aliyun_Log_Models_PutLogsRequest($this->project, $this->logstore, $topic, $source, $logitems);
			$res2 = $this->client->putLogs($req2);
			return $this->client->putLogs($req2) ? 1 : 0;
		} catch (\Aliyun_Log_Exception $e) {
			return array(
				'code' => $e->getErrorCode(),
				'message' => $e->getErrorMessage(),
			);
		}

		return 0;
	}

	/**
	 * 创建logStore
	 * @param $logStore
	 * @return int
	 * @throws \Aliyun_Log_Exception
	 */
	public function createLogStore($logStore = '') {
		if (empty($logStore)) {
			return 0;
		}
		$req2 = new Aliyun_Log_Models_CreateLogstoreRequest($this->project, $logStore, 3, 2);
		$res2 = $this->client->createLogstore($req2);
		sleep(60);
		return $res2;
	}

	/**
	 * 查询列出当前 project 下的所有日志库名称
	 * @param string $project
	 * @return \Aliyun_Log_Models_ListLogstoresResponse
	 * @throws \Aliyun_Log_Exception
	 */
	public function getAllLogStore($project = '') {
		if (empty($project)) {
			$project = $this->project;
		}
		$req1 = new \Aliyun_Log_Models_ListLogstoresRequest($project);
		$res1 = $this->client->listLogstores($req1);
		return $res1;
	}
}