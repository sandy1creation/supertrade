<?php
class Grid_model extends CI_Model
{
	var $grid='';
	var $gridID=0;
	var $gridKey='';
	var $requiredClass='';
	var $requiredClassOjbect='';
	var $header=array();
	var $column=array();
	var $search=array();
	var $requestValues=array();
	var $request=array();
	var $default_search='';
	var $output='';
	var $forceCond='';
	function make_grid($condVar,$request,$Template,$excel='')
	{
		//print_r($condVar);die;
		// Call the Model constructor
        parent::__construct();
        $this->load->model('dbmodel');
		
		$this->db=$this->dbmodel;
		$condVar=trim($condVar);
		if($condVar=='' || $condVar==null) return false;
		if(!is_numeric($condVar))
		{
			$cond='grid_key="'.$condVar.'"';
		}
		else
		{
			$cond='id="'.$condVar.'"';
		}
		//print_r($request);die;
		if(is_array($request) && count($request)>0)
		foreach($request as $key=>$value)
		{	
			if($value!='')
			{
			 $k = "__".strtoupper(str_replace("_", "",trim($key)))."__";		
			 $this->requestValues[$k]=trim($value);	
			
			}
		
		}	
		$session=$_SESSION;
		foreach($session as $key => $value)
		{
			if(!is_array($value))
			{
			 $k = "session[".strtoupper(str_replace("_", "",trim($key)))."]";		
			 $this->requestValues[$k]=trim($value);	
		 }
		}
		
		$this->request=$request;
		$this->request['grid_key']=$condVar;
	
		$grid=$this->generateGrid($cond,$excel);
		
		
		return $grid;
	}
	function generateGrid($cond,$excel)
	{
		//echo "select * from gridlist where ".$cond." order by id desc";
		$result=$this->select("*",$cond,"sys_gridlist");
		//print_r($result);die('hey'); 	
		$result=$result['row'];
		if(isset($result->id))
		{
			$this->gridData=$result;
			$this->gridID=$result->id;	
			$this->gridData->newpageadd=$this->parsing($this->gridData->newpageadd);
			$this->requiredClass=$result->classname;
			if(isset($result->defaultsearch) && $result->defaultsearch!='')
			{
				
				 $this->default_search=$this->parsing($result->defaultsearch);
				}
			
		}
		else
		return false;
		$column=$this->get_column($excel);
		
	}
	function parsing($string)
	{
		//print_r($string);die('hey');
		if(is_array($this->requestValues) && count($this->requestValues)>0)
		{
			foreach($this->requestValues as $key=>$value)
			{
				$string=str_replace($key,$value,$string);
			}
			//print_r($string);die('hey');
			return $string;	
		}
		else
		{
			return $string;	
		}	
	}
	function get_column($excel)
	{
		//$this->db->QUERYREVIEW=TRUE;
		$result=$this->select("*","id_grid=".$this->gridID,"grid_column","all","LENGTH(position),position");
		
		if(is_array($result) && count($result)>0)
		{ 
			foreach($result['rows'] as $rs)
			{
				if(is_object($rs))
				{
				if(isset($rs->modifier) && $rs->modifier!='')
				{
					$requiredClass=($this->requiredClass=='')?'modifier':$this->requiredClass;
					$object=$this->load->model($requiredClass);
					
					$this->requiredClassOjbect['obj']=$this->$requiredClass;
					
					$this->requiredClassOjbect['modifier'][$rs->alliasname]=$rs->modifier;
					
				}	
				$this->header[]=$rs->field_key;
				if($rs->alliasname!='')
				{
					$this->column[]=$rs->field_name.' as '.$rs->alliasname;
					$alls[$rs->alliasname]=$rs->field_name;
					$this->search[$rs->alliasname]=$rs->field_key;
				}
				else
				{
					$this->column[]=$rs->field_name;
					$this->search[$rs->field_name]=$rs->field_key;
				}
				}
			}
			
		}
		
		$cond=array();
		if($this->default_search!='')
		$cond[]=$this->default_search;
		$trailing_search='';
		if(isset($this->request['sk']) && $this->request['keyword']!='')
		{
			$sk=$this->request['sk'];
			if(isset($alls) && is_array($alls) && count($alls)>0)
			{
			
				$this->request['sk']=isset($alls[$this->request['sk']])?$alls[$this->request['sk']]:$this->request['sk'];
			}
			$cond[]="".trim($this->request['sk'])." like '%".trim($this->request['keyword'])."%'";
			$trailing_search='/sk/'.trim($sk).'/keyword/'.trim($this->request['keyword']);
			
			
		}
		if(isset($_GET['sk']) && $_GET['keyword']!='')
		{
			
			$get=$_GET;
			if(isset($alls) && is_array($alls) && count($alls)>0)
			{
			
				$get['sk']=isset($alls[$get['sk']])?$alls[$get['sk']]:$get['sk'];
			}
			$cond[]="".trim($get['sk'])." like '%".trim($get['keyword'])."%'";
			$trailing_search='/sk/'.trim($_GET['sk']).'/keyword/'.trim($_GET['keyword']);
			
			
		}
		$cond= implode(" AND ",$cond);
		$keyword=isset($_GET['keyword'])?$_GET['keyword']:'';
		$sk=isset($_GET['sk'])?$_GET['sk']:'';
		if(isset($this->request['QUERYREVIEW']) && $this->request['QUERYREVIEW'])
		$this->db->QUERYREVIEW=TRUE;
		
		if(isset($this->request['page']) && $this->request['page']>0)
		{
			$page=(int)$this->request['page'];	
		}
		else
		$page=1;
		//$this->db->QUERYREVIEW=TRUE;
		if(isset($this->forceCond) && $this->forceCond!='')
		{
			if($cond!='')
			$cond=$cond.' AND '.$this->forceCond;
			else
			$cond=$this->forceCond;	
		}
		if($excel=='print')
		{
			$rt=$this->select("".implode(',',$this->column)."",$cond,$this->gridData->grid_table,"all",'id DESC');	
		}
		else
		$rt=$this->select("".implode(',',$this->column)."",$cond,$this->gridData->grid_table,"all",'id DESC',$page);	
		//print_r($rt);
		
		foreach($rt['rows'] as $r => $row)
			{
				foreach($row as $key=>$value)
				{
					
					if(isset($this->requiredClassOjbect['modifier'][$key]) )
					{
						
						$mod=$this->requiredClassOjbect['modifier'][$key];
						
						$rt['rows'][$r]->$key=$this->requiredClassOjbect['obj']->$mod($value);
					}
				}
			}
			
		if($excel=='download')
		{
		
			//$this->db->QUERYREVIEW=TRUE;
			$rt=$this->select("".implode(',',$this->column)."",$cond,$this->gridData->grid_table,"all",'id DESC');	
			$filename = "data_" . date('Ymd') . ".csv";

			foreach($rt['rows'] as $r => $row)
			{
				foreach($row as $key=>$value)
				{
					
					if(isset($this->requiredClassOjbect['column']) && $this->requiredClassOjbect['column']==$key)
					{
						$mod=$this->requiredClassOjbect['modifier'];
						$rt['rows'][$r]->$key=$this->requiredClassOjbect['obj']->$mod($value);
					}
				}
			}
				

			 
			
			// Open php output stream and write headers
		$fp = fopen('php://output', 'w');
		if ($fp && $result) {
			header('Content-Type: text/csv');
			header('Content-Disposition: attachment; filename='.$filename);
			header('Pragma: no-cache');
			header('Expires: 0');
			echo "Data for requested table\n\n";
			// Write mysql headers to csv
			fputcsv($fp, $this->header);
			$row_tally = 0;
			// Write mysql rows to csv
			foreach($rt['rows'] as $row)
			{
				$row=objectsToArray($row);
				$row_tally = $row_tally + 1;
			
				fputcsv($fp, array_values($row));
			}
		}
			  exit;	
		}
		$this->load->library('pagination');


		$action=$this->select("*","id_grid=".$this->gridID,"grid_actions","all");	
	
		if(is_array($action['rows']) && count($action['rows'])>0) 
		{
			$this->header[]='Action';
			
		foreach($action['rows'] as $act)
		{
			
			if(is_object($act))
			{
				
				if(strpos("asfas".$act->url,'EXTERNAL')==true)
				{
					
					$arr[]="<a href='".str_replace('EXTERNAL/',$this->config->item('front_url'),$this->parsing($act->url))."' ><i class='".$act->classname."'></i>".$act->action_name.'dfdd'."</a>";
				
				}
				else
				{
				if(strtolower(trim($act->action_name))=='delete')
				{
					$arr[]="<a onclick='javascript:return confirm(\"Are you really want to delete this record? \");' href='".site_url($this->parsing($act->url))."/page/$page".$trailing_search."' ><i class='".$act->classname."'></i>".$act->action_name."</a>";	
				}
				else
				{
					$arr[]="<a href='".site_url($this->parsing($act->url))."/page/$page".$trailing_search."#form1' ><i class='".$act->classname."'></i>".$act->action_name."</a>";
					//print_r($arr);die('hey');
				}
				}	
			}
		}
		//print_r($act);die('hey');
	}
		if(isset($trailing_search))
		{
		$rt['pagination']['trailing_search']=$trailing_search;}
		else
		$rt['pagination']['trailing_search']='';
		
		$rt['pagination']['page']=$page;
		
		$this->gridtemplate=array("header"=>$this->header,"keyss"=>$this->column,"search"=>$this->search,"columns"=>$rt,'gaction'=>isset($arr)?$arr:'','gridData'=>$this->gridData,'ske'=>$sk,'keyword'=>$keyword,'pagination'=>isset($rt['pagination'])?$rt['pagination']:'','grid_key'=>$this->request['grid_key']);	
		foreach($this->gridtemplate as $key=>$value)
		{
			
			$data[$key]=$value;
			//print_r($data[$key]);die;
		}

	if($excel=='print')
		{
			
			 echo $this->output = $this->load->view($this->config->item('template_name').'grid_printtemplate',$data,true);
			 echo "<script>window.print();</script>";
			//echo "<script>window.close();</script>";
		}
		else
		{	
			
			if($this->gridData->grid_template!=''):
			$this->output = $this->load->view('admin/'.$this->gridData->grid_template,$data,true);
			else:
			$this->output = $this->load->view($this->config->item('template_name').'grid_template',$data,true);
			//$this->output = $this->load->view('admin/grid_template',$data,true);
			endif;
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
	function select($col,$cond,$tab,$type='one',$orderby='id DESC',$page=0)
	{ 
		//print_r($page);die('so');
		return $this->db->select_data($tab,$col,$cond,$orderby,$page);
	}
	function count($query)
	{
		return $this->get_var($query);
	}
	function selectFull($query)
	{
		return $this->get_results($query);	
	}
	
	function delete($cond,$table)
	{
	return $this->deleteQuery($cond,$table);
	}
	
}
 function cleanData(&$str)
  {
    // escape tab characters
    $str = preg_replace("/\t/", "\\t", $str);

    // escape new lines
    $str = preg_replace("/\r?\n/", "\\n", $str);

    // convert 't' and 'f' to boolean values
    if($str == 't') $str = 'TRUE';
    if($str == 'f') $str = 'FALSE';

    // force certain number/date formats to be imported as strings
    if(preg_match("/^0/", $str) || preg_match("/^\+?\d{8,}$/", $str) || preg_match("/^\d{4}.\d{1,2}.\d{1,2}/", $str)) {
      $str = "'$str";
    }

    // escape fields that include double quotes
    if(strstr($str, '"')) $str = '"' . str_replace('"', '""', $str) . '"';
  }
  function objectsToArray( $object )
			{
				if( !is_object( $object ) && !is_array( $object ) )
				{
					return $object;
				}
				if( is_object( $object ) )
				{
					$object = get_object_vars( $object );
				}
				return array_map( 'objectsToArray', $object );
			}

?>
