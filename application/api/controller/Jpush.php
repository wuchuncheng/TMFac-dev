<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/26
 * Time: 11:27
 */

namespace app\api\controller;



use app\api\model\PushModel;
use think\Db;
use think\facade\Request;

class Jpush extends Base
{
    /**
     * 获取推送列表数据
     */
    public function getPushList(){
        //判断请求方式以及请求参数
        $inputData = Request::get();
        $method = Request::method();
        $params = ['index'];
        $ret = checkBeforeAction($inputData, $params, $method, 'GET', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $condition = [];
//        $condition['member_code'] = '';
        $pushModel = new PushModel();
        if(!empty($inputData['member_code'])){
            $condition['member_code'] = $inputData['member_code'];
        }

        //分页处理
        $count = $pushModel->countPush($condition);
        $pageSize = empty($inputData['page_size']) ? 20 : $inputData['page_size'];
        $firstRow = $pageSize * ($inputData['index'] - 1);
        $total = ceil($count / $pageSize);
        $limit = $firstRow.','.$pageSize;

        //获取数据
        $field = 'push_id, push_content, create_time';
        $list = $pushModel->getPushList($condition, $field, $limit);
        if($list === false){
            return reJson(500, '获取列表失败', []);
        }

        //处理数据
        foreach ($list as &$v){
            $v['push_content'] = json_decode($v['push_content']);
            $v['create_time'] = date('Y-m-d H:i:s', $v['create_time']);
        }

        $data = [
            'list' => $list,
            'total_page' => $total,
            'total' => $count
        ];

        return reJson(200, '获取列表成功', $data);
    }

    /**
     * 删除消息
     */
    public function deletePush(){
        //判断请求方式以及请求参数
        $inputData = Request::delete();
        $method = Request::method();
        $params = ['push_id'];
        $ret = checkBeforeAction($inputData, $params, $method, 'DELETE', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        $pushModel = new PushModel();
        $re = $pushModel->deletePush(['push_id' => $inputData['push_id']]);
        if($re === false){
            return reJson(500, '删除失败', []);
        }

        return reJson(200, '删除成功', []);
    }

    /**
     * 推送全部
     */
    public function pushAll(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['title', 'content'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        Db::startTrans();
        //记录消息
        $pushModel = new PushModel();
        $data = [
            'push_content' => json_encode($inputData),
            'create_time' => time(),
        ];
        $push = $pushModel->addPush($data);
        if($push === false){
            Db::rollback();
            return reJson(500, '新增推送消息失败', []);
        }

        //推送消息
        $extras = $inputData;
        $JPush = new \app\extend\controller\Jpush();
        $re = $JPush::JPushAll($extras);
        if($re['http_code'] != 200){
            Db::rollback();
            return reJson(500, '推送失败', $re);
        }

        Db::commit();
        return reJson(200, '推送成功', []);
    }

    /**
     * 推送单个
     */
    public function pushOne(){
        //判断请求方式以及请求参数
        $inputData = Request::post();
        $method = Request::method();
        $params = ['title', 'content', 'member_code'];
        $ret = checkBeforeAction($inputData, $params, $method, 'POST', $msg);
        if(!$ret){
            return reJson(500, $msg, []);
        }

        Db::startTrans();
        //记录消息
        $pushModel = new PushModel();
        $data = [
            'push_content' => json_encode($inputData),
            'member_code' => $inputData['member_code'],
            'create_time' => time(),
        ];
        $push = $pushModel->addPush($data);
        if($push === false){
            Db::rollback();
            return reJson(500, '新增推送消息失败', []);
        }

        //推送消息
        $extras = $inputData;
        $extras['alias'] = $inputData['member_code'];
        unset($extras['member_code']);
        $JPush = new \app\extend\controller\Jpush();
        $re = $JPush::JPushOne($extras);
        if($re['http_code'] != 200){
            Db::rollback();
            return reJson(500, '推送失败', $re);
        }

        Db::commit();
        return reJson(200, '推送成功', []);
    }
}