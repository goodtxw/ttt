<?php

namespace Admin\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        if(!empty(session('admin_id'))){
            $data = M('admin')->where(array('id'=>session('admin_id')))->select();
            $data1 = $data[0]['name'];
            if($data[0]['level'] == 0){
                $this->assign('data1',$data1);
            }else{
                $this->assign('data1',$data1);
            }
            $this->display('Index/index');
        }else{
            $this->redirect('Login/login');
        }
    }
}