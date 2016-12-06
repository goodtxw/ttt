<?php

    namespace Admin\Controller;
    use Admin\Model\IntegralModel;
    use Admin\Model\UserModel;
    use Think\Controller;
    use Think\Page;

    class IntegralController extends CommonController
    {
        // 显示每个用户的所有积分
        public function index()
        {
            $inte = new IntegralModel();
            // 分页
            $count = $inte->group('u_id')->count();
            // 设置分页  总页数/每页数量
            $page = new Page($count,10);
            // 设置上一页下一页
            $page->setConfig('prev', '上一页');
            $page->setConfig('next', '下一页');
            // 显示分页
            $show = $page->show();
            // 按照分页查询数据
            $list = $inte->relation(true)->field(array('sum(integral)'=>'suminte','u_id'))->group('u_id')->limit($page->firstRow.','.$page->listRows)->select();

            $this->assign('list', $list);
            $this->assign('page', $show);
            $this->display('Integral/index');
        }

        // 显示单个用户的积分详情
        public function list($id)
        {
            $inte = new IntegralModel();
            // 根据用户id获取用户的积分详情
            $data = $inte->relation(true)->where(['u_id'=>['eq', $id]])->select();

            $this->assign('list', $data);
            $this->display('Integral/list');
        }

        // 按照用户id搜索
        public function integralSearch($user_id)
        {
            $inte = new IntegralModel();
            $list = $inte->relation(true)->where(['u_id'=>['eq',$user_id]])->field(array('sum(integral)'=>'suminte','u_id'))->group('u_id')->select();

            $this->assign('list', $list);
            $this->display('Integral/integralSearch');
        }
    }