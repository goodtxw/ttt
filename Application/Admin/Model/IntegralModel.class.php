<?php

    namespace Admin\Model;
    use \Think\Model\RelationModel;

    class IntegralModel extends RelationModel
    {
        // protected $fields = array('id', 'name', 'sex', 'age');
        protected $pk = 'id';
        protected $_validate = array(
            //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
            //在新增时验证name是否唯一
//            array('name','','用户名已存在!!ヽ(o_ _)o摔倒',0,'unique',1),
        );

        protected $_link = array(
            'User' => array(
                'mapping_type' => self::BELONGS_TO,
                'mapping_name' => 'User',
                'class_name' => 'User',
                'foreign_key' => 'u_id',
                'as_fields' => 'name'
            ),
        );
    }