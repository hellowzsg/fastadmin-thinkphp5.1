<?php

namespace app\admin\controller\wechat;

use addons\wechat\library\Wechat;
use app\common\controller\Backend;

/**
 * 资源管理
 *
 * @icon fa fa-list-alt
 */
class Response extends Backend
{

    protected $model = null;
    protected $searchFields = 'id,title';

    public function initialize()
    {
        parent::initialize();
        $this->model = model('WechatResponse');
    }

    /**
     * 选择素材
     */
    public function select()
    {
        return $this->view->fetch();
    }

    /**
     * 添加
     */
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['eventkey'] = isset($params['eventkey']) && $params['eventkey'] ? $params['eventkey'] : uniqid();
            $params['content'] = json_encode($params['content']);
            $params['createtime'] = time();
            if ($params) {
                $this->model->save($params);
                $this->success();
                $this->content = $params;
            }
            $this->error();
        }
        $appConfig = Wechat::appConfig();
        $this->view->applist = $appConfig;
        return $this->view->fetch();
    }

    /**
     * 编辑
     */
    public function edit($ids = null)
    {
        $row = $this->model->get($ids);
        if (!$row) {
            $this->error(__('No Results were found'));
        }

        if ($this->request->isPost()) {
            $params = $this->request->post("row/a");
            $params['eventkey'] = isset($params['eventkey']) && $params['eventkey'] ? $params['eventkey'] : uniqid();
            $params['content'] = json_encode($params['content']);
            if ($params) {
                $row->save($params);
                $this->success();
            }
            $this->error();
        }
        $this->view->assign("row", $row);
        $appConfig = Wechat::appConfig();
        $this->view->applist = $appConfig;
        return $this->view->fetch();
    }

}