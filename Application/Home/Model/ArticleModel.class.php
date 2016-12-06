<?php
    namespace Home\Model;

    use Think\Model\RelationModel;

    class ArticleModel extends RelationModel
    {
        protected $_link = array(
            'User' => array(
                'mapping_type' => self::BELONGS_TO,
                'mapping_name' => 'User',
                'class_name' => 'User',
                'foreign_key' => 'u_id',
                'as_fields' => 'head_image,name:username',
            ),
            'Image' => array(
                'mapping_type' => self::HAS_MANY,
                'mapping_name' => 'Image',
                'class_name' => 'Image',
                'foreign_key' => 'article_id',
            ),
            'Comment' => array(
                'mapping_type' => self::HAS_MANY,
                'mapping_name' => 'Comment',
                'class_name' => 'Comment',
                'foreign_key' => 'article_id'
            ),
            'Zan' => array(
                'mapping_type' => self::HAS_MANY,
                'mapping_name' => 'Zan',
                'class_name' => 'Zan',
                'foreign_key' => 'article_id'
            ),
        );
    }