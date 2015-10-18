<?php
class Form_model extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';
    var $table    = '';
    var $validation='';
    var $form_err='';

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->model('dbmodel');
         $this->load->library('form_validation');
    }
    function validate($post)
	{
		$gotpost=$post;
		unset($gotpost['submit']);
		unset($gotpost['action']);
		unset($gotpost['id']);
		if(isset($gotpost['xmlcode']))
		$gotpost['xmlcode']=urlencode($gotpost['xmlcode']);
		$this->dbData=$gotpost;
		//print_r($this->dbData);die('soffsfa');
		//print_r($_FILES);die;
		if(is_array($_FILES) && count($_FILES)>0)
		{
			foreach($_FILES as $key => $value)
			{
				if(isset($_FILES[$key]["tmp_name"]) && $_FILES[$key]["tmp_name"]!='')
				$this->dbData[$key]=$this->upload_files($_FILES[$key]);
			}
		}
		$this->php_validations($this->dbData);
	  
		if(!is_array($this->form_err) || count($this->form_err)<=0)
		return true;
		else
		return false;
	}
	function upload_files($file)
	{
		 $tmp_name = $file["tmp_name"];
       		 $name = date("YmdHis").$file["name"];
       		 if(move_uploaded_file($tmp_name, "assets/products/$name"))
       		 {
       		 	return "assets/products/$name";
       		 }
       		 return '';
	}
   function php_validations($dbData)
    { 
		$validation=$this->validation;
		
		if(isset($validation) && is_array($validation) && count($validation))
		foreach($validation as $key=>$validation_rule)
		{
			switch($validation_rule)
			{  
			  case 'NOTNULL':	
			     if(empty($dbData[$key]))
				   {
					 $this->form_validation->set_rules($key, $key, 'required'); 
					 $this->form_err[$key]= '<span class="error"> Please enter a value</span>';
				   } 
			  break;	
			 
			case 'AGE':
			     if(empty($dbData[$key]))
				   {
					  $this->form_validation->set_rules($key, $key, 'required|numeric|min_length[2]'); 
					  $this->form_err[$key]= '<span class="error"> Please enter a value</span>';
				   } 
				 else if(!is_numeric($dbData[$key]))
				   {
				      $this->form_validation->set_rules($key, $key, 'required|numeric|min_length[2]'); 
				      $this->form_err[$key]= '<span class="error"> Data entered was not numeric</span>';
				   } 
				 else if(strlen($dbData[$key]) != 2) 
				   {
				      $this->form_validation->set_rules($key, $key, 'required|min_length[2]'); 
				      $this->form_err[$key] = '<span class="error"> The number entered was not 2 digits long</span>';
				   } 
				
			   break;
				
			 case 'EMAIL':
				 if(empty($dbData[$key]))
				   {
					  $this->form_validation->set_rules($key, $key, 'required'); 
					  $this->form_err[$key]= '<span class="error"> Please enter a value</span>';
				   } 
				 elseif (!$dbData[$key]=="")
				   {  
					  $email = $dbData[$key];
					 
				 if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
				   { 
					  $this->form_validation->set_rules($key, $key, 'required|valid_email');	
					  //$this->form_err[$key] ="Valid email address."; 
				   } 
				 else 
				   { 
					  $this->form_validation->set_rules($key, $key, 'required|valid_email');
					  $this->form_err[$key] ="Invalid email address."; 
				   } 
					
				   }
			 break;
					
		   case 'PHONENOTNULL':
				if(empty($dbData[$key]))
				   {
					 $this->form_validation->set_rules($key, $key, 'required|min_length[10]');
					 $this->form_err[$key]= '<span class="error"> Please enter a value</span>';
				   } 
				else if(!is_numeric($dbData[$key]))
				   {
					 $this->form_validation->set_rules($key, $key, 'required|numeric|min_length[10]');   
				     $this->form_err[$key]= '<span class="error"> Data entered was not numeric</span>';
				   } 
				else if(strlen($dbData[$key]) != 10) 
				   {
				     $this->form_validation->set_rules($key, $key, 'required|min_length[10]');
				     $this->form_err[$key] = '<span class="error"> The number entered was not 10 digits long</span>';
				   } 
				 
				break;
				
			case 'PASSWORD':
				if(empty($dbData['image']))
				   {
					 $this->form_err[$key]= '<span class="error"> Please enter a value</span>';
				   } 
				else if(!is_numeric($dbData['image']))
				   {
				     $this->form_err[$key]= '<span class="error"> Data entered was not numeric</span>';
				   } 
				 
			 break;	
		 }
	   }
	}
	function insert($lastData,$return=true,$tablename='')
	{
		
		return $this->query->insertQuery($lastData,$return,$tablename);
	}
	function update($lastData,$cond,$tablename='')
	{
		
		return $this->updateQuery($lastData,$cond,$tablename);
	}
	function select($col,$cond,$tab,$type='one',$orderby='id DESC')
	{
		return $this->selectQuery($col,$cond,$tab,$type,$orderby);
	}
	function count($query)
	{
		return $this->get_var($query);
	}
	function checkBalance($amount)
	{
		$credit=$this->dbmodel->select_data('addfund',"sum(fund) as credit","type='credit' and user_id=".$_SESSION['user_id']); 
		$debit=$this->dbmodel->select_data('addfund',"sum(fund) as debit","type='debit' and user_id=".$_SESSION['user_id']); 
		
		$ad_bal=$credit[0]->credit-$debit[0]->debit;
		if($ad_bal>$amount)
		return true;
		else
		return false;
	}
	
	function delete($cond,$table,$ds='')
	{
		
		if(isset($ds->default_search) && $ds->default_search!='')
			$add_ds=" AND ".$ds->default_search;
		else
			$add_ds='';
	$this->dbmodel->delete_data($table,$cond.$add_ds);
	
	header("Location:".$_SERVER['HTTP_REFERER']);
	}
	function convert_xmltoform($xml_array,$id=0,$aaction='add',$form_err='',$ds='')
	{
		//print_r($form_err);
		$db=$this->dbmodel;
		if(is_array($xml_array) && count($xml_array)>0)
		{
			if($id>0)
			{
				if(isset($ds->default_search) && $ds->default_search!='')
				$add_ds=" AND ".$ds->default_search;
				else
				$add_ds='';
			
			$result=$db->select_data($xml_array['table'],"*","id=".$id.$add_ds); 
			if($result['count']==0){
			$this->form_err['id']= '<span class="error"> Please dont break the rules</span>';}
			$result=$result['row'];
			}
			$action=isset($xml_array['form']['@attributes']['action'])?$xml_array['form']['@attributes']['action']:'';
			$method=isset($xml_array['form']['@attributes']['method'])?$xml_array['form']['@attributes']['method']:'post';
			$enctype=isset($xml_array['form']['@attributes']['enctype'])?$xml_array['form']['@attributes']['enctype']:'multipart/form-data';
			$class=isset($xml_array['form']['@attributes']['class'])?$xml_array['form']['@attributes']['class']:'';
			$str['form']['startForm']='<form name="form1" id="form1" action="'.$action.'" method="'.$method.'" enctype="'.$enctype.'" class="'.$class.'" >';
			if(isset($xml_array['form']['field']) && is_array($xml_array['form']['field']) && count($xml_array['form']['field'])>0)
			{
				$i=0;
				if(isset($xml_array['form']['field'][0]))
				$xml_last_array=$xml_array['form']['field'];
				else
				$xml_last_array[0]=$xml_array['form']['field'];
				foreach($xml_last_array as $field)
				{
					if(isset($field['validation']) && $field['validation']!='' & !is_array($field['validation']))
					{
						
						$validation[$field['fieldid']]=strtoupper(str_replace(array(" ","_","."),"",$field['validation']));
						$val_string=$this->getJqueryValidation($validation[$field['fieldid']])." ";
					}
					else
					$val_string='';
					$field_type=isset($field['fieldtype'])?$field['fieldtype']:'text';
					$db_fields[$i]['field']=$field['fieldid'];
					$db_fields[$i]['fieldname']=isset($field['fieldname'])?$field['fieldname']:'';
					$db_fields[$i]['type']=isset($field['dbtype'])?$field['dbtype']:'';
					switch($field_type)
					{
						case 'hidden':
						switch($field['fieldid'])
						{
							case 'id':
							$str['form']['hidden'][$i]['field']='<input type="hidden" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" value="'.$id.'" >';
							if(isset($form_err[$field['fieldid']]))
							{
								$str['form']['hidden'][$i]['field'].='<br/><span style="color:red">'.$form_err[$field['fieldid']].'</span>';
							}
							break;
							case 'action':
							$str['form']['hidden'][$i]['field']='<input type="hidden" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" value="'.$aaction.'" >';
							break;
							default:
								$str['form']['hidden'][$i]['field']='<input type="hidden" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" value="'.$field['default_value'].'" >';
						}
						$str['form']['hidden'][$i]['type']=$field_type;
						$str['form']['hidden'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'text': 
						
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$dis='';
						if(isset($field['disabled']) && $field['disabled']=='true')
						{
							$dis='disabled';
						}
						
						$str['form']['fields'][$i]['field']='<input type="text" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$val_string.''.$field['classname'].'" value="'.(isset($result->$field['fieldid'])?$result->$field['fieldid']:"").'" '.$dis.'>';
						//print_r($form_err);die;
						if(isset($form_err[$field['fieldid']]))
						{
							$str['form']['fields'][$i]['field'].='<br/><span style="color:red">'.$form_err[$field['fieldid']].'</span>';
						}
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						
						$i++;
						break;
						case 'date': 
						
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						
						$str['form']['fields'][$i]['field']='<input type="text" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" onClick="datepicker(\''.$field['fieldid'].'\')" value="'.(isset($result->$field['fieldid'])?$result->$field['fieldid']:"").'"><script> $jq("#'.$field['fieldid'].'").trigger("click");</script>';
						//print_r($form_err);die;
						if(isset($form_err[$field['fieldid']]))
						{
							$str['form']['fields'][$i]['field'].='<br/><span style="color:red">'.$form_err[$field['fieldid']].'</span>';
						}
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						
						$i++;
						break;
						case 'password': 
						
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						
						$str['form']['fields'][$i]['field']='<input type="password" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$val_string.''.$field['classname'].'" value="">';
						//print_r($form_err);die;
						if(isset($form_err[$field['fieldid']]))
						{
							$str['form']['fields'][$i]['field'].='<br/><span style="color:red">'.$form_err[$field['fieldid']].'</span>';
						}
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						
						$i++;
						break;
						case 'textarea':
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$row='';
						if(isset($field['rows']))
						{
							$row="rows = '".$field['rows']."'";	
						}
						$is_encoded = preg_match('~%[0-9A-F]{2}~i', (isset($result->$field['fieldid'])?$result->$field['fieldid']:""));
						
						
						$str['form']['fields'][$i]['field']='<textarea '.$row.' name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].' '.$val_string.'" >'.(isset($result->$field['fieldid'])?(($is_encoded)?urldecode($result->$field['fieldid']):$result->$field['fieldid']):"").'</textarea>';
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'text_editor':
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$row='';
						if(isset($field['rows']))
						{
							$row="rows = '".$field['rows']."'";	
						}
						$is_encoded = preg_match('~%[0-9A-F]{2}~i', (isset($result->$field['fieldid'])?$result->$field['fieldid']:""));
						
						
						$str['form']['fields'][$i]['field']='<textarea '.$row.' name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" onfocus="ckeditor(\''.$field['fieldid'].'\')" class="'.$field['classname'].'" >'.(isset($result->$field['fieldid'])?(($is_encoded)?urldecode($result->$field['fieldid']):$result->$field['fieldid']):"").'</textarea><script> $jq("#'.$field['fieldid'].'").trigger("focus");</script>';
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'select':
						//print_r($field);
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$column=isset($field['column'])?$field['column']:'*';
						$condition=isset($field['condition'])?$field['condition']:'';
						$table=isset($field['table'])?$field['table']:'';
						$fstr='<select name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].' '.$val_string.'" >';
						$fstr.='<option value="">Select '.$field['fieldname'].'</option>';
						if(!is_array($column))
						{
							//$this->QUERYREVIEW=TRUE;
							$col=explode(",",trim($column));
							if(isset($field['concat']))
							{
								$result_select=$db->select_data($table,$col[0].",CONCAT(".$col[1].",' , ',".$field['concat'].") as ".$col[1],$condition);
							}
							else
							$result_select=$db->select_data($table,$column,$condition);
							$result_select=$result_select['rows'];
							if(is_array($result_select) && count($result_select)>0)
								{
									
									foreach($result_select as $res_select)
									{
										if(isset($result->$field['fieldid']) && $res_select->id==$result->$field['fieldid'])
										{
											$fstr.='<option value="'.$res_select->$col[0].'" selected="selected">'.$res_select->$col[1].'</option>';
										}
										else
										$fstr.='<option value="'.$res_select->$col[0].'">'.$res_select->$col[1].'</option>';	
									}	
								}	
						}
						else
						{
							if(is_array($column) && count($column)>0)
								foreach($column as $col)
								{
									$lc=explode("=>",$col);
									//print_r($lc);die();
									if(isset($result->$field['fieldid']) && $lc[0]==$result->$field['fieldid'])
									{
										$fstr.='<option value="'.$lc[0].'" selected="selected">'.$lc[1].'</option>';
									}
									else
									$fstr.='<option value="'.$lc[0].'">'.$lc[1].'</option>';
								}
						}
						
					
						$fstr.='</select>';
						$str['form']['fields'][$i]['field']=$fstr;
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'checkbox':
						$checked=(isset($result->$field['fieldid']) && $result->$field['fieldid']==$field['value'])?'checked="checked"':'';
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$str['form']['fields'][$i]['field']='<input type="checkbox" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].'" value="'.$field['value'].'" '.$checked.'>';
						
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'radio':
						$checked=(isset($result->$field['fieldid']) && $result->$field['fieldid']==$field['value'])?'checked="checked"':'';
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$str['form']['fields'][$i]['field']='<input type="radio" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].'" value="'.$field['value'].'" '.$checked.' >';
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'multi_radio':
						
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$column=isset($field['column'])?$field['column']:'*';
						$condition=isset($field['condition'])?$field['condition']:'';
						$table=isset($field['table'])?$field['table']:'';
						$all_radio='';
						if(!is_array($column) && $table!='')
						{
							//$this->QUERYREVIEW=TRUE;
							$col=explode(",",trim($column));
							if(isset($field['concat']))
							{
								$result_select=$db->select_data($table,$col[0].",CONCAT(".$col[1].",' , ',".$field['concat'].") as ".$col[1],$condition);
							}
							else
							$result_select=$db->select_data($table,$column,$condition);
							$result_select=$result_select['rows'];
							if(is_array($result_select) && count($result_select)>0)
								{
									
									foreach($result_select as $res_select)
									{
										$checked=(isset($result->$field['fieldid']) && $res_select->$col[1]==$result->$field['fieldid'])?'checked="checked"':'';
										$all_radio.='<input type="radio" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].'" value="'.$res_select->$col[1].'" '.$checked.' > '. $res_select->$col[1]. "  ";
										
									}	
								}	
						}
						else
						{
							$multi_value=explode(",",$field['value']);
						
							for($ij=0;$ij < count($multi_value); $ij++)
							{
								$checked=(isset($result->$field['fieldid']) && $result->$field['fieldid']==$multi_value[$ij])?'checked="checked"':'';
								$all_radio.='<input type="radio" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].'" value="'.$multi_value[$ij].'" '.$checked.' > '. $multi_value[$ij]. "  ";
							}
							
						}
						
						$str['form']['fields'][$i]['field']=$all_radio;
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
						case 'file':
						if(isset($result->$field['fieldid']))
						$image='<br/><img src="'.base_url($result->$field['fieldid']).'" style="height:100px;" />';
						else
						$image='';
						$str['form']['fields'][$i]['label']='<label for="'.$field['fieldid'].'" >'.$field['fieldname'].'</label>';
						$str['form']['fields'][$i]['field']='<input type="file" name="'.$field['fieldid'].'" id="'.$field['fieldid'].'" class="'.$field['classname'].'" >'.$image;
						$str['form']['fields'][$i]['type']=$field_type;
						$str['form']['fields'][$i]['id']=$field['fieldid'];
						$i++;
						break;
					}	
					
					
				}	
				if(isset($db_fields) && is_array($db_fields) && count($db_fields)>0)
				{
					
					//$result=$db->db_query("SHOW TABLES FROM ".$this->db->database);
					//print_r($result);die;
					if(isset($xml_array['table']) && $xml_array['table']!=''):
					$create='CREATE TABLE IF NOT EXISTS `sys_'.$xml_array['table'].'` (
					  `id` BIGINT(20) NOT NULL AUTO_INCREMENT,';
					  $check_table="SHOW TABLES LIKE 'sys_".$xml_array['table']."'";
					  $check = $db->db_query($check_table);
					 
					if($check['count']>0):
					$show='SHOW COLUMNS FROM sys_'.$xml_array['table'];
					$query = $db->db_query($show);
					$table_fields=array();
					if(isset($query['rows']))
					foreach($query['rows'] as $q)
					{
						$table_fields[]=$q->Field;
						$after_field=$q->Field;	
					}
					endif;
					
					  foreach($db_fields as $df)
					  {
						 
						  if($df['field']=='id' || $df['field']=='action')
						  continue;
						  $df['type']=strtolower(str_replace(array("_"," ",".","-"),"",trim($df['type'])));
						switch($df['type'])
						{
							case 'varchar':  
								if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` varchar(200) NOT NULL AFTER  '.$after_field);
								}
								$create .='`'.$df['field'].'` varchar(200) NOT NULL,';
							break;
							case 'bigint': 
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` BIGINT(200) NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` BIGINT(200) NOT NULL,';
							break;
							case 'int':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` int(4) NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` int(4) NOT NULL,';
							break;
							case 'tinyint':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` tinyint(2) NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` tinyint(2) NOT NULL,';
							break;
							case 'date':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` DATE NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` DATE NOT NULL,';
							break;
							case 'datetime':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` DATETIME NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` DATETIME NOT NULL,';
							break;
							case 'decimal':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` DECIMAL( 10, 2 ) NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` DECIMAL( 10, 2 ) NOT NULL,';
							break;
							case 'mediumtext':  
							if(!in_array($df['field'],$table_fields) && count($table_fields)>0)
								{
									$db->db_query('ALTER TABLE  sys_'.$xml_array['table'].' ADD  `'.$df['field'].'` MEDIUMTEXT NOT NULL AFTER  '.$after_field);
								} 
								$create .='`'.$df['field'].'` MEDIUMTEXT NOT NULL,';
							break;
						}	
						
						
					}
					
					
					  $create .= '`createdby` BIGINT( 20 ) NOT NULL,
					  `creationdate` DATETIME NOT NULL,
					  `modifiedby` BIGINT( 20 ) NOT NULL,
					  `modificationdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
					  PRIMARY KEY (`id`)
					) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;';
					$grid_key=isset($xml_array['grid_key'])?$xml_array['grid_key']:$xml_array['table'];
					$query = $db->db_query($create);
					$gridExist=$db->select_data("gridlist","id","grid_key='".$grid_key."'");
					if(!isset($gridExist['row']->id) && $grid_key!='')
					{
						
					$gridData['grid_key']=$grid_key;
					$gridData['name']='Manage '.ucfirst(str_replace('_'," ",$xml_array['table']));
					$gridData['grid_table']='sys_'.$xml_array['table'];
					$gridData['orderby']='id DESC';
					$url=explode("/",$_SERVER['REQUEST_URI']);
					$lurl=str_replace(array(".html",".php"),"",$url[count($url)-1]);
					$lurl_filter=explode("?",$lurl);
					$lurl=$lurl_filter[0];
					$gridData['newpageadd']=$lurl;
					if($id=$this->insertQuery($gridData,true,'gridlist'))
					{
						$i=1;
							$columnData['id_grid']=$id;
							$columnData['field_key']='ID';
							$columnData['field_name']='id';
							$columnData['fieldlength']=10;
							$columnData['position']=$i;
							$this->insertQuery($columnData,true,'grid_column');
							$i++;
						foreach($db_fields as $df)
						{
							if($df['field']=='id' || $df['field']=='action')
							continue;
							$columnData['id_grid']=$id;
							$columnData['field_key']=$df['fieldname'];
							$columnData['field_name']=str_replace(array(".",":"),'',$df['field']);
							$columnData['fieldlength']=20;
							$columnData['position']=$i;
							$this->insertQuery($columnData,true,'grid_column');
							$i++;
						}
							$actionData['id_grid']=$id;
							$actionData['action_name']='Edit';
							$actionData['url_name']='Edit';
							$actionData['url']=$xml_array['table'].'/index/id/{id}/action/edit';
							$actionData['classname']='fam-pencil';
							$actionData['position']=1;
							$this->insertQuery($actionData,true,'grid_actions');
							$actionData['id_grid']=$id;
							$actionData['action_name']='Delete';
							$actionData['url_name']='Delete';
							$actionData['url']=$xml_array['table'].'/index/id/{id}/action/delete';
							$actionData['classname']='fam-cross';
							$actionData['position']=1;
							$this->insertQuery($actionData,true,'grid_actions');
							
					}
					}
					endif;
				}
				$this->validation=isset($validation)?$validation:'';
				$str['form']['endForm']='</form>';
				
				return $str;
			}
			
		}
			
	}
	function unique_check($field,$table,$record,$cond='')
	{
		if($cond!='')
		$condition=" AND ".$cond;
		else
		$condition='';
		$result=$this->dbmodel->select_data($table,$field,$field."='".$record."'".$condition); 
		if($result['count']>0)
		{
			return false;
		}
		return true;
	}
	function insertQuery($data,$return,$table)
	{
		$db=$this->dbmodel;
		return $db->insert_data($table,$data,$return);
	}
	function updated($data,$cond,$table)
	{
		$db=$this->dbmodel;
		return $db->update_data($table,$data,$cond);
	}
	function getJqueryValidation($val)
	{
		switch($val)
		{
			case 'NOTNULL':
			return	"validate[required]";
			break;
			case 'PHONENOTNULL':
			return "validate[required,custom[phone],maxSize[10]]";
			case 'AGE':
			return "validate[required,custom[onlyNumberSp],minSize[1],maxSize[2]]";
			case 'EMAIL':
			return "validate[required,custom[email]]";
			break;
			case 'PASSWORD':
			return "validate[required,equals[password]]";
			break;
			case 'Letters':
			return "validate[required,custom[onlyLetterSp]]";
			break;
			case 'NUMBERS':
			return "validate[required,custom[onlyNumberSp]]";
			break;
		}
	}
	function himanshu($id)
	{
			return "Sandy the great";
	}
}
