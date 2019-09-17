<?php

namespace app\admin\controller\general;

use app\admin\library\Log;
use app\admin\model\User;
use app\common\controller\Backend;

/**
 * 日志管理
 *
 * @icon fa fa-user
 */
class Logs extends Backend
{
    protected $log = null;

    public function initialize()
    {
        $this->log = new Log();
        parent::initialize();
    }

    public function index()
    {
        if (false === $this->log->isAllow()) {
            die($this->log->getError());
        }
        $directory = $this->log->getDirectory();
        if ($this->request->isAjax()) {
            $filePaths = isset($_GET['file_paths']) ? $_GET['file_paths'] : [];
            $whereFilter = request()->param('filter');
            $whereFilter = $whereFilter ? json_decode($whereFilter, true) : [];
            $rows = $this->log->getLogs($filePaths, $whereFilter);
            $info = $this->log->getInfo($filePaths);
            if (false === $rows) {
                $this->error('获取失败');
            }
            return json(['rows' => $rows, 'info' => $info]);
        }
        $this->assign(compact('directory'));
        return $this->fetch();
    }

    public function del($ids = "")
    {
        $filePaths = input('file_paths');
        if ($ids && $filePaths) {
            $rows = $this->log->deleteForId($filePaths, explode(',', $ids));
            $this->success('', '', $rows);
        }
        $this->error(__('Parameter %s can not be empty', 'ids'));
    }
}