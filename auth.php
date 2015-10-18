<?php
class Auth extends CI_Model {

	var $dbuse='';
    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->model('dbmodel');
        @session_start();
    }
    function user_login($table,$column,$cond)
    {
		
    		//$this->dbmodel->QUERYREVIEW=true;
		$res=$this->dbmodel->select_data($table,$column,$cond);	
		$result=$res['row'];
		
		if(isset($result->id) && $result->id>0)
		{
			$this->rand_id=$result->id;
			$data['userid']=$result->id;
			$data['ip_address']=$_SERVER['REMOTE_ADDR'];
			$data['login_time']=date("Y-m-d H:i:s");
			$data['session_id']=session_id();
			$data['status']=1;
			
			$log_id=$this->dbmodel->insert_data('userlog',$data,true);
			if($log_id>0)
			{
				session_destroy();
				session_start();
				$_SESSION['user_id']=$result->id;
				$_SESSION['log_id']=$log_id;
				$_SESSION['username']=$result->name;
				$_SESSION['email']=$result->email;
				$_SESSION['is_admin']=isset($result->is_admin)?$result->is_admin:0;
				$_SESSION['user_role']=$this->user_roles();
					
			}
			//print_r($_SESSION);die;
			return true;	
		}
		return false;
	}
    function check_user()
    {
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']>0)
		{
			$data[]='userid='.$_SESSION['user_id'];
			$data[]='ip_address="'.$_SERVER['REMOTE_ADDR'].'"';
			$data[]='id="'.$_SESSION['log_id'].'"';
			$data[]='session_id="'.session_id().'"';
			$data[]='status=1';
			
			$result=$this->dbmodel->select_data('userlog','*',$data);	
			//$this->dbmodel->QUERYREVIEW=TRUE;
			$menu=$this->dbmodel->select_data('menu','id','url="'.$this->uri->segment(1).'"');	
			//$this->dbmodel->QUERYREVIEW=TRUE;
			
			$user=array();
			$user['count']=0;
			if(is_array($_SESSION['user_role']) && count($_SESSION['user_role'])>0)
			{
				if(is_array($menu['rows']) && count($menu['rows'])>0)
				$user=$this->dbmodel->select_data('menutorole','*','FIND_IN_SET(`id_role`,"'.implode(",",$_SESSION['user_role']).'") AND id_menu='.$menu[0]->id);	
			}
			
			if($_SESSION['is_admin']==1)
			$user['count']=1;
			if($result['count']>0 && $user['count']>0)
			{
				return true;
			}
			return false;
		}
		return false;
	}
    function check_grid($key)
    {
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']>0)
		{
			$data[]='userid='.$_SESSION['user_id'];
			$data[]='ip_address="'.$_SERVER['REMOTE_ADDR'].'"';
			$data[]='id="'.$_SESSION['log_id'].'"';
			$data[]='session_id="'.session_id().'"';
			$data[]='status=1';
			
			$result=$this->dbmodel->select_data('userlog','*',$data);	
			$menu=$this->dbmodel->select_data('gridlist','id','grid_key="'.$key.'"');	
			//$this->dbmodel->QUERYREVIEW=TRUE;
			$user=array();
			$user['count']=0;
			if(is_array($_SESSION['user_role']) && count($_SESSION['user_role'])>0)
			{
				if(is_array($menu['rows']) && count($menu['rows'])>0)
				$user=$this->dbmodel->select_data('gridtorole','*','FIND_IN_SET(`id_role`,"'.implode(",",$_SESSION['user_role']).'") AND id_grid='.$menu[0]->id);	
			}
			if($_SESSION['is_admin']==1)
			$user['count']=1;
			if($result['count']>0 && $user['count']>0)
			{
				return true;
			}
			return false;
		}
		return false;
	}
	function user_roles()
	{
			$result=$this->dbmodel->select_data('usertorole','id_role',"id_user='".$_SESSION['user_id']."'");
			if(isset($result['rows']) && is_array($result['rows']) && count($result['rows'])>0)
			{
				foreach($result['rows'] as $rs)
				{
					$final_roles[]=$rs->id_role;	
				}	
				return $final_roles;
			}
			return false;	
	}
    function logout()
    {
		if(isset($_SESSION['user_id']) && $_SESSION['user_id']>0)
		{
			$data=array();
			$data[]='userid='.$_SESSION['user_id'];
			$data[]='ip_address="'.$_SERVER['REMOTE_ADDR'].'"';
			$data[]='id="'.$_SESSION['log_id'].'"';
			$data[]='session_id="'.session_id().'"';
			$data[]='status=1';
			$result=$this->dbmodel->select_data('userlog','*',$data);	
			if($result['count']>0)
			{
				$data=array();
				$data['userid']=$_SESSION['user_id'];
			$data['ip_address']=$_SERVER['REMOTE_ADDR'];
			$data['id']=$_SESSION['log_id'];
			$data['session_id']=session_id();
			$data['status']=1;
				$result=$this->dbmodel->update_data('userlog','status=0',$data);
				unset($_SESSION);
				session_destroy();
				return true;
			}
			return false;
		}
	}
	function balance()
	{
			return 'asdfasdfashi';
	}
}
?>
