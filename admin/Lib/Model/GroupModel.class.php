<?php

class GroupModel extends Model
{	
  // 自动填充设置
  protected $_auto = array(
        array('status', '1', self::MODEL_INSERT),
        array('create_time', 'time', self::MODEL_INSERT, 'function'),
        array('update_time', 'time', self::MODEL_UPDATE, 'function')
   );

}
?>