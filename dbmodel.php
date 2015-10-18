<?php
class Dbmodel extends CI_Model {

    var $title   = '';
    var $content = '';
    var $date    = '';
    var $table    = '';
    var $QUERYREVIEW =0;
    var $PERPAGE =10;

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }
    function select_data($table='',$column='*',$cond='',$orderby='id DESC',$page=0,$glue=' AND ',$limit='')
    {  
		
		
		if(is_array($column) && count($column)>0)
		$column=implode(",",$column);
		if($table=='')
		$table=$this->table;
		if(is_array($cond) && count($cond)>0)
		{
			$lcond= implode(" ".$glue." ",$cond);	
		}
		else
		$lcond=$cond;
		
		if($lcond!='')
		$lcond=' where '.$lcond;
	
		//$this->db->select($column);
		//$this->db->from($table);
		
		if(is_array($limit) && count($limit)>0)
		{
			$limit='Limit '.$limit[0].",".$limit[1];	
		}
		else
		$limit='';
		if(is_array($lcond) && count($lcond)>0)
		$this->db->where($lcond); 
		//$last_limit=is_array($limit)?implode(',',$limit):$limit;
		//$this->db->limit('1020',0);
		//$query = $this->db->get();
		$table=str_replace('sys_','',$table);
		$col_first=explode(',',$column);
		$array=array();
		$arr=array();
		$page_array=array();
		if($page>0)
		{
			$col_first[0]=($col_first[0]!='')?$col_first[0]:'id';
			$query=$this->db->query("Select count(".$col_first[0].") as count from ".$this->db->dbprefix.$table."".$lcond." order by ".$orderby);
			$row=mysql_fetch_object($query->result_id);
			$count=$row->count;
			$per_page = $this->PERPAGE;
			$start = (int)($page-1) * $per_page;
			$last=$per_page;
			$total_pages=ceil($count/$per_page);
			$column=($column!='')?$column:'*';
			$query=$this->db->query("Select ".$column." from ".$this->db->dbprefix.$table."".$lcond." order by ".$orderby." Limit ".$start.",".$last);
			//echo $sql = $this->db->last_query();die;
			$page_array['pagination']['first']='1';
			$page_array['pagination']['last']=$total_pages;
		}
		else
		$query=$this->db->query("Select ".$column." from ".$this->db->dbprefix.$table."".$lcond." order by ".$orderby." ".$limit);
		//print_r( $query);
		//print $this->db->last_query();
		 //$this->db->coount_all_results('my_table');
		 if($this->QUERYREVIEW)
		 {
		 echo $sql = $this->db->last_query();die;
		}
		$i=0;
		
		while($row=mysql_fetch_object($query->result_id))
		{
			$arr[$i]=$row;	
			$i++;
		}
		$array=$arr;
		$array['row']=isset($arr[0])?$arr[0]:'';
		$array['rows']=$arr;
		$array['count']=$i;
		$array=array_merge($array,$page_array);
		//echo $sql = $this->db->last_query();die;
		return $array;
		//$query = $this->db->get_where('admin', array('id' => 1),1);	
	}
    function select_singledata($table='',$column='*',$cond='',$orderby='id DESC',$limit=array(0,10),$page=1,$glue=' AND ')
    {
		
		if($table=='')
		$table=$this->table;
		$lcond=$this->stringtoarray($cond,$glue);
		
		$this->db->select($column);
		$this->db->from($table);
		
		if(is_array($lcond) && count($lcond)>0)
		$this->db->where($lcond); 
		$last_limit=is_array($limit)?implode(',',$limit):$limit;
		//$this->db->limit($last_limit);
		$array=array();
		 //$this->db->count_all_results('my_table');
		 //return $query->row_array();
		//$str = $this->db->last_query();
		//print_r($str);
		return $query->row_object();
	}
	private function stringtoarray($cond='',$glue=' AND ')
	{
		
		$lcond=array();
		if($cond!='' && is_string($cond))
		{
			$c=explode($glue,$cond);
			foreach($c as $cd)
			{
				$ld=explode("=",$cd);
				if(isset($ld[1]))
				$lcond[$ld[0]]=str_replace(array("'",'"'," "),'',$ld[1]);	
				else
				$lcond[$ld[0]]='';
			} 
		}
		if(is_array($cond) && count($cond)>0)
		{
			$lcond=$cond;	
		}
		return $lcond;	
	}
	public function insert_data($table,$data,$returnID=false)
	{   
		if($table=='')
		$table=$this->table;
		$return=$this->db->insert($table,$data);
		if($returnID==true)
		return $this->db->insert_id();
		else
		return $return;

	}
	public function update_data($table,$data,$cond,$glue=' AND ')
	{
		$lcond=$this->stringtoarray($cond,$glue);
		$ldata=$this->stringtoarray($data,$glue);
		$this->db->where($lcond);
		$this->db->update($table, $ldata); 
		//echo $sql = $this->db->last_query();die;
		return true;	

	}
	public function delete_data($table,$cond,$glue=' AND ')
	{
		$lcond=$this->stringtoarray($cond,$glue);
		$this->db->where($lcond);
		$this->db->delete($table); 
		//echo $sql = $this->db->last_query();die;
	}
	public function db_query($query)
	{
		$arr=array();
		$q=$this->db->query($query);
		$i=0;
		if(is_object($q)):
		while($row=mysql_fetch_object($q->result_id))
		{
			$arr[$i]=$row;	
			$i++;
		}
		$array=$arr;
		$array['row']=isset($arr[0])?$arr[0]:'';
		$array['rows']=$arr;
		$array['count']=$i;
	
		//echo $sql = $this->db->last_query();die;
		return $array;
		endif;
		//echo $sql = $this->db->last_query();die;
	}
}
?>
