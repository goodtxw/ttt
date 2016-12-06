<?php

namespace Home\Controller;

class PersonaldataController extends BaseController
{

    //修改个人资料
    public function index($e = 0)
    {
        if($e == 1){
            $this->assign('error',1);
        }elseif ($e == 2){
            $this->assign('error',2);
        }elseif ($e == 3){
            $this->assign('error',3);
        }elseif ($e == 4){
            $this->assign('error',4);
        }

        $personal_info = M('user')->where(array('id'=>session('id')))->select();
        $date = intval((time()-$personal_info[0]['create_time'])/86400);
        //学校信息
        $school = M('school')->where(array('u_id'=>session('id')))->select();
        $link = M('flink')->where(array('show'=>1))->select();
        //消息提醒
        $remind = M('friend_request')->where(array('r_id'=>session('id'),'agree'=>0))->count();
        $this->assign('remind',$remind);
        $this->assign('link',$link);
        $this->assign('school',$school[0]);
        $this->assign('date',$date);
        $this->assign('info',$personal_info[0]);
        $this->display('Personaldata/index');
    }

    //执行修改
    public function update()
    {
        //用户名不为空
        if($_POST['name'] == ''){
            $this->redirect('index\e\1');
            die;
        }
        //正则匹配手机号
        if(!preg_match('/^[1][3578][0-9]{9}$/',$_POST['tel'])){
            $this->redirect('index\e\4');
            die;
        }

        M('user')->name = $_POST['name'];
        M('user')->tel = $_POST['tel'];
        M('user')->address = $_POST['address'];
        M('user')->sex = $_POST['sex'];
        M('user')->birthday = $_POST['birthday'];
        M('user')->privacy = $_POST['privacy'];
        if(M('user')->where(array('id'=>session('id')))->save()>0){
            $this->redirect('index\e\2');
        }else{
            $this->redirect('index\e\3');
        }

    }

    //更新学校信息
    public function school()
    {
        //先判断id，有更新，没有添加
        if(empty($_POST['id'])){
            $data = [];
            $data['middle_school'] = $_POST['middle_school'];
            $data['high_school'] = $_POST['high_school'];
            $data['college'] = $_POST['college'];
            $data['middle_start'] = $_POST['middle_start'];
            $data['high_start'] = $_POST['high_start'];
            $data['college_start'] = $_POST['college_start'];
            $data['u_id'] = session('id');

            //实例化对象
            $school = M('school');
            //过滤数据,数据验证
            if (!$school->create($data)) {
                //如果创建数据失败,表示验证没有通过
                //输出错误信息 并且跳转
                $this->error($school->getError());
            } else {
                //验证通过 执行添加操作
                //执行添加
                if ($school->add($data) > 0) {
                    //学校信息
                    $school = M('school')->where(array('u_id'=>session('id')))->select();
                    //个人的基本信息
                    $personal_info = M('user')->where(array('id'=>session('id')))->select();
                    //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
                    $date = intval((time()-$personal_info[0]['create_time'])/86400);

                    $this->assign('date',$date);
                    $this->assign('info',$personal_info[0]);
                    $this->assign('school',$school[0]);
                    $this->assign('success',2);
                    $this->display('Personaldata/index');
                } else {
                    $this->error($school->getError());
                }
            }
        }else{
            M('school')->middle_school = $_POST['middle_school'];
            M('school')->middle_start = $_POST['middle_start'];
            M('school')->high_school = $_POST['high_school'];
            M('school')->high_start = $_POST['high_start'];
            M('school')->college = $_POST['college'];
            M('school')->college_start = $_POST['college_start'];
            M('school')->where(array('id'=>$_POST['id']))->save();

            //学校信息
            $school = M('school')->where(array('u_id'=>session('id')))->select();
            //个人的基本信息
            $personal_info = M('user')->where(array('id'=>session('id')))->select();
            //用户注册天数 intval((当前时间-注册时间)/一天的秒数) 一天86400秒
            $date = intval((time()-$personal_info[0]['create_time'])/86400);

            $this->assign('date',$date);
            $this->assign('info',$personal_info[0]);
            $this->assign('school',$school[0]);
            $this->assign('success',1);
            $this->display('Personaldata/index');
        }
    }
}