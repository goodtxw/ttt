<?php
namespace Home\Model;

    use Think\Model\RelationModel;

    class FriendGroupModel extends RelationModel
    {
        protected $_link = array(
            'friend' => array(
                'mapping_type' => self::HAS_MANY,
                'mapping_name' => 'Friend',
                'class_name' => 'Friend',
                'foreign_key' => 'fg_id',
            ),
        );
    }