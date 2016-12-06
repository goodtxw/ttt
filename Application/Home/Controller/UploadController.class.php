<?php
    namespace Home\Controller;

    use Think\Upload;

    class UploadController extends BaseController
    {
        // 上传文件
        public function upload()
        {
            $upload = new Upload();
            //使用子目录保存文件 默认true
            $upload->autoSub = false;
            // 设置附件上传根目录
            $upload->rootPath  = C('LOCAL_PATH') .'/upload/';
            $upload->saveName = array('uniqid','');
            // 上传文件
            $info  =  $upload->upload();
            if(!$info) {
                // 上传错误提示错误信息 $upload->getError()
                echo $upload->getError();
            }else{
                // 上传成功
                echo json_encode(['code'=>1,'content'=>$info]);
            }
        }

        // 删除文件
        public function delete($name,$key)
        {
            if (unlink(C('LOCAL_PATH') . '/upload/' . $name)) {
                echo 0;
            }else {
                echo 1;
            }
        }

        // 上传文件
        public function uploadImg()
        {
            $upload = new Upload();
            //使用子目录保存文件 默认true
            $upload->autoSub = false;
            // 设置附件上传根目录
            $upload->rootPath  = C('LOCAL_PATH') .'/upload/';
            $upload->saveName = array('uniqid','');
            // 上传文件
            $info  =  $upload->upload();
            if(!$info) {
                // 上传错误提示错误信息 $upload->getError()
                echo $upload->getError();
            }else{
                $data = [];
                $data['name'] = $info['file']['savename'];
                $data['article_id'] = 0;
                $data['album_id'] = $_POST['album_id'];
                $data['cover'] = 0;
                $data['time'] = time();
                $data['ban'] = 0;
                $data['u_id'] = session('id');
                if(M('image')->add($data)){
                    // 上传成功
                    echo json_encode(['code'=>1,'content'=>$info]);
                }
            }
        }

        // 删除文件
        public function deleteImg($name,$key)
        {
            if (unlink(C('LOCAL_PATH') . '/upload/' . $name)) {
                M('image')->where(['name'=>['eq',$name]])->delete();
                echo 0;
            }else {
                echo 1;
            }
        }
    }