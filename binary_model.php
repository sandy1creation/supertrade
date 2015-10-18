<?php
class Binary_model extends CI_Model {

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
function downline_number($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

        if($this->count_downline($member,$position) >0 ){
        $total=$this->total_members_down($query['row']->id);
        }else{
        $total=0;
        }

        return $total;      

          

    }   

function count_downline($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

         return $rows = $query['count'];

         
    }   

function total_members_down($upline,$reset=0) {
global $num;
if ($reset==0) { $num=1; }
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
                   foreach($query['rows'] as $rows) {
                    $num++;
                    $this->total_members_down($rows->id,$num);
                    } 
                    return $num;
           } else { 
            return $num;
            }
		}

  

function total_down($upline) {
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 


        return $rows = $query['count'];

       
    }
    function level($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

        if($this->count_downline($member,$position) >0 ){
        $total=$this->total_members_down_ids($query['row']->id);
        }else{
        $total=array();
        }
		
        return $total;      

          

    }
    function total_members_down_ids($upline,$reset=0) {
global $numm;
if ($reset==0) { $numm[$upline]=array(); }
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
                   foreach($query['rows'] as $rows) {
                    $numm[$upline][$rows->side]=$rows->id;
                    $this->total_members_down_ids($rows->id,$numm);
                    } 
                    return $numm;
           } else { 
            return $numm;
            }
		}
    function level_array($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

        if($this->count_downline($member,$position) >0 ){
        $total=$this->total_members_down_array($query['row']->id);
        }else{
        $total=array();
        }
		
        return $total;      

          

    }
    function total_members_down_array($upline,$reset=0) {
global $num_array;
if ($reset==0) { $num_array[]=$upline; }
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
                   foreach($query['rows'] as $rows) {
                    $num_array[]=$rows->id;
                    $this->total_members_down_array($rows->id,$num_array);
                    } 
                    return $num_array;
           } else { 
            return $num_array;
            }
		}
    function total_downline($upline,$reset=0)
    {
		global $n;
if ($reset==0) { $numm[$upline]=array(); }
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
                   foreach($query['rows'] as $rows) {
                    $n[]=$rows->id;
                    $this->total_downline($rows->id,$n);
                    } 
                    return $n;
           } else { 
            return $n;
            }
	}
	function get_id($upline=0,$position)
	{
		if($upline>0)
		{
		$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline." AND side='".$position."'");
		
		if(isset($query['row']->id))
		{
			$query['row']->free=$this->checkfree($query['row']->id);
			return $query['row'];	
		} 
	}	
	}
	function checkfree($id)
	{
		$query=$this->dbmodel->select_data('pins',"id","used_by=".$id." AND is_used=1");
		
		if($query['count']==0)
		{
			return 1;
		}
		else 
		return 0;
	}
	 function lastlevel($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

        if($this->count_downline($member,$position) >0 ){
        $total=$this->total_members_down_last($query['row']->id,0,$position);
        }else{
        $total=$member;
        }
		
        return $total;      

          

    }
    function total_members_down_last($upline,$reset=0,$position) {
global $lastnumm;

if ($reset==0) { $lastnumm=$upline; }
$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
                   foreach($query['rows'] as $rows) {
					  
					 if($position==$rows->side)
                     $lastnumm=$rows->id;
                    $this->total_members_down_last($rows->id,$lastnumm,$position);
                    } 
                  
                    return $lastnumm;
           } else { 
            return $lastnumm;
            }
		}
		function upParent($id,$position='')
		{
			$db=$this->dbmodel;
			if($id>0)
			{
				$query=$this->dbmodel->select_data('users',"*","id=".$id); 
				
				if($query['count']>0)
				{
					$position=strtolower($position);
					$upline=$query['row']->parent_id;
					$up_position=$query['row']->side;
					$t=$query['row']->$position+1;
					$this->dbmodel->update_data('users',array(strtolower($position)=>$t),"id=".$id);	
					//$this->dbmodel->QUERYREVIEW=true;
					$this->upParent($upline,$up_position);
				}
			}
			else
			{
				return true;
			}
		}
		function pairPayment($id,$caping)
		{
			$db=$this->dbmodel;
			if($id>0)
			{
				$query=$this->dbmodel->select_data('users',"*","id=".$id); 
				$upline=$query['row']->parent_id;
				$this->getPayment($id,$caping);
				$this->pairPayment($upline,$caping);
			}
		}
		function getPayment($id,$caping)
		{
			$query=$this->dbmodel->select_data('users',"id,parent_id,sponsered_id,side","sponsered_id=".$id);
			
			$q=$query;
			$sl=0;
			$sr=0;
			foreach($q['rows'] as $spons)
			{
				$pin_payment=$this->dbmodel->select_data('pins',"*","used_by=".$spons->id);
				if($pin_payment['count']>0):
				if($spons->side=='Left')
				$sl=$sl+1;	
				else
				$sr=$sr+1;
				endif;
			}		
			if(($sl>1 || $sr>1) && $sl>0 && $sr>0)
			{
				
					$res=$this->dbmodel->select_data('pins',"*","used_by=".$id);
					if(isset($res['row']->amount))
					{
						
						
					$pair=$this->dbmodel->select_data('users',"*","id=".$id." AND level>0");
						if($pair['count']==0)
						{
							$up['level']=1;
							if($sr>1)
							{
								$up['start']='right';
							}
							$pt=$this->dbmodel->select_data('payment',"sum(amount) as total","DATE(`modificationdate`)< DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND id_user=".$id);
							$total=$pt['row']->total+(($res['row']->amount/100)*5);
							if($caping>=$total)
							$this->insertQuery(array('id_user'=>$id,'remarks'=>'Pair Point Level 1','amount'=>($res['row']->amount/100)*5),false,'payment');
							$this->dbmodel->update_data('users',$up,"id=".$id);
						}
						
						
						
						
					}
					
							$this->oneratioone($id,$caping);
						
				
				
			}
			else
			{
				$this->oneratioone($id,$caping);
				return true;	
			}
		}
		function oneratioone($id,$caping)
		{
			
			$query=$this->dbmodel->select_data('users',"*","parent_id=".$id);
			if($id==1)
			{
				if($query['count']==2)
			{
				
					
					$left=$this->downline_number_paid($id,'left');	
					
					$right=$this->downline_number_paid($id,'right');	
					
				
				
				if($left>0 or $right>0)
				{
					
					$res=$this->dbmodel->select_data('pins',"*","used_by=".$id);
					if(isset($res['row']->amount))
					{
						
						
						
						$pair=$this->dbmodel->select_data('users',"*","id=".$id." AND level>0");
						
						if(isset($pair['row']->start))
						{
							
							if($pair['row']->start=='left')
							{
								
								$level=$this->dbmodel->select_data('users',"*","id=".$id);
								$up=array();
								$up['level']=$level['row']->level+1;
								
									$lastu=$up['level']+1;
									
										$last=3;
									for($i=2;$i<=$up['level'];$i++)
									{
										$last=$last+2;	
									}
									
									$total=$left+$right;
									
								if($left>=$lastu && $right>=$up['level'])
								{
									if($total>=$last)
									{
										
										$pt=$this->dbmodel->select_data('payment',"sum(amount) as total","DATE(`modificationdate`)< DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND id_user=".$id);
										$total=$pt['row']->total+(($res['row']->amount/100)*5);
										if($caping>=$total)
										$this->insertQuery(array('id_user'=>$id,'remarks'=>'Pair Point Level '.$up['level'],'amount'=>($res['row']->amount/100)*5),false,'payment');
										$this->dbmodel->update_data('users',$up,"id=".$id);
										
									}
									
								}	
							}
							else
							{
								
								$level=$this->dbmodel->select_data('users',"*","id=".$id);
								$up=array();
								$up['level']=$level['row']->level+1;
								
								
									
										$lastu=$up['level']+1;
									
										$last=3;
									for($i=2;$i<=$up['level'];$i++)
									{
										$last=$last+2;	
									}
									
									$total=$left+$right;
									
								if($right>=$lastu && $left>=$up['level'])
								{
									if($total>=$last)
									{
										
											$pt=$this->dbmodel->select_data('payment',"sum(amount) as total","DATE(`modificationdate`)< DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND id_user=".$id);
											$total=$pt['row']->total+(($res['row']->amount/100)*5);
											if($caping>=$total)
											$this->insertQuery(array('id_user'=>$id,'remarks'=>'Pair Point Level '.$up['level'],'amount'=>($res['row']->amount/100)*5),false,'payment');
											$this->dbmodel->update_data('users',$up,"id=".$id);
										
									}
									
								}	
							}
							
						}
					}
				
				}
			}
			else
			{
				return true;	
			}	
			}
			if($query['count']==2)
			{
				foreach($query['rows'] as $res)
				{
					
					
					$left=$this->downline_number_paid($res->id,'left');	
					
					$right=$this->downline_number_paid($res->id,'right');	
					
				
					$check_id=$res->id;
				if($left>0 or $right>0)
				{
					
					$res=$this->dbmodel->select_data('pins',"*","used_by=".$check_id);
					if(isset($res['row']->amount))
					{
						
						
					$pair=$this->dbmodel->select_data('users',"*","id=".$check_id." AND level>0");
					
						if(isset($pair['row']->start))
						{
							
							if($pair['row']->start=='left')
							{
								
								$level=$this->dbmodel->select_data('users',"*","id=".$check_id);
								$up=array();
								$up['level']=$level['row']->level+1;
								
									$lastu=$up['level']+1;
									
										$last=3;
									for($i=2;$i<=$up['level'];$i++)
									{
										$last=$last+2;	
									}
									
									$total=$left+$right;
									
								if($left>=$lastu && $right>=$up['level'])
								{
									
									if($total>=$last)
									{
										
											$pt=$this->dbmodel->select_data('payment',"sum(amount) as total","DATE(`modificationdate`)< DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND id_user=".$check_id);
											$total=$pt['row']->total+(($res['row']->amount/100)*5);
											if($caping>=$total)
										$this->insertQuery(array('id_user'=>$check_id,'remarks'=>'Pair Point Level '.$up['level'],'amount'=>($res['row']->amount/100)*5),false,'payment');
										$this->dbmodel->update_data('users',$up,"id=".$check_id);
										
									}
									
								}	
							}
							else
							{
								$level=$this->dbmodel->select_data('users',"*","id=".$check_id);
								$up=array();
								$up['level']=$level['row']->level+1;
									$lastu=$up['level']+1;
									
										$last=3;
									for($i=2;$i<=$up['level'];$i++)
									{
										$last=$last+2;	
									}
									
									$total=$left+$right;
									/*echo $check_id."<br/>";
									echo $total."<br/>";
									echo $right."<br/>";
									echo $left."<br/>";
									echo $lastu."<br/>";*/
								if($right>=$lastu  && $left>=$up['level'])
								{
									
									if($total>=$last)
									{
										
										$pt=$this->dbmodel->select_data('payment',"sum(amount) as total","DATE(`modificationdate`)< DATE_SUB(CURDATE(), INTERVAL 15 DAY) AND id_user=".$check_id);
											$total=$pt['row']->total+(($res['row']->amount/100)*5);
											if($caping>=$total)
										$this->insertQuery(array('id_user'=>$check_id,'remarks'=>'Pair Point Level '.$up['level'],'amount'=>($res['row']->amount/100)*5),false,'payment');
										
										$this->dbmodel->update_data('users',$up,"id=".$check_id);
									}
									
								}	
							}
							
						}
					}
				}
				}
			}
			else
			{
				return true;	
			}
								
		}
		function insertQuery($data,$return,$table)
		{
			$db=$this->dbmodel;
			
			return $db->insert_data($table,$data,$return);
		}
		function payment($id)
		{
			$p=$this->dbmodel->select_data('payment',"sum(amount) as total","id_user=".$id);
			return $p['row']->total;
		}
		function downline_number_paid($member,$position) {

$query=$this->dbmodel->select_data('users',"*","parent_id=".$member." AND side='".$position."'"); 

        if($this->count_downline($member,$position) >0 ){
        $total=$this->total_members_down_paid($query['row']->id);
        }else{
        $total=0;
        }
		
        return $total;      

          

    }
    function total_members_down_paid($upline,$reset=0) 
    {
		global $num_paid;
		if ($reset==0) { $num_paid=1; }
		$query=$this->dbmodel->select_data('users',"*","parent_id=".$upline); 

		if(isset($query['rows']) && is_array($query['rows']) && count($query['rows'])>0){
				foreach($query['rows'] as $rows) 
				{
							   $pin_payment=$this->dbmodel->select_data('pins',"*","used_by=".$rows->id);
							if($pin_payment['count']>0):
								$num_paid++;
							endif;	
                    $this->total_members_down_paid($rows->id,$num_paid);
                 } 
                    return $num_paid;
          } 
           else { 
            return $num_paid;
          }
	}
} 
    ?>
