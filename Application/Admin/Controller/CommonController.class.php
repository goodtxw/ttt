<?php
    namespace Admin\Controller;
    use Think\Controller;

    class CommonController extends Controller
    {
        public function _empty($name = '')
        {
            $this->show('404');
        }
    }