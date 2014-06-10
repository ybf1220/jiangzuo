<?php

class AdminModel extends RelationModel
{
	protected $_link=array(
	   'Role'=>array(
	       'mapping_type'  => BELONGS_TO,
	       'class_name'    => 'Role',
	 	   'mapping_name'=>'role',	
           'foreign_key'   => 'role_id',
	   ),
	);
}