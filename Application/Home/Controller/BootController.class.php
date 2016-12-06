<?php
namespace Home\Controller;

class BootController extends BaseController
{
    public function index()
    {
        $link = M('flink')->where(array('show'=>1))->select();
        $this->assign('link',$link);
        $this->display('Boot/index');
    }
}