<?php

    namespace Admin\Model;
    use \Think\Model\RelationModel;

    class UserModel extends RelationModel
    {
        // protected $fields = array('id', 'name', 'sex', 'age');
        protected $pk = 'id';
        protected $_validate = array(
            //array(验证字段1,验证规则,错误提示,[验证条件,附加规则,验证时间]),
            //在新增时验证name是否唯一
//            array('name','','用户名已存在!!ヽ(o_ _)o摔倒',0,'unique',1),
        );

        // user模型与integral模型之间一对多关系   HAS_MANY
        protected $_link = array(
            'Integral' => array(
                'mapping_type' => self::HAS_MANY,
                'mapping_name' => 'Integral',
                'class_name' => 'Integral',
                'foreign_key' => 'u_id',
            ),
        );
    }