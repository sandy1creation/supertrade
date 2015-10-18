<?php
class Ajax_model extends CI_Model {

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
    
    	function upload_files($file, $id)
    	{
				    $obj=$this->form_model;
			        $prod_id=$id;
			        $_FILES=$file;		
					$i=0;
					foreach($_FILES as $k=>$file) {
						if($k != "video") {
							$name = $prod_id.'_'.$file['name'];
							$size = $file['size'];
							$usr_dir = '../video/usr_'.(int)$_SESSION['user_id'];
							if(!is_dir($usr_dir))
							mkdir($usr_dir, 0777, true);
							
							$new_dir = 'prod_'.$prod_id;
							$original_path = $usr_dir.'/'.$new_dir;
							if(!is_dir($original_path))
							mkdir($original_path, 0777, true);
							
							$tmp = $file['tmp_name'];
						
							if(move_uploaded_file($tmp, $original_path.'/'.$name))
							{
								  $videoname['video']=$name;
								  $obj->updated($videoname,"id=".$id,'product');
							      if ($i == 0)break;
							   }
							
							 }					
						}
						$url = '';

							if (isset($this->request->get['filter_name'])) {
								$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
							}

							if (isset($this->request->get['filter_model'])) {
								$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
							}

							if (isset($this->request->get['filter_price'])) {
								$url .= '&filter_price=' . $this->request->get['filter_price'];
							}

							if (isset($this->request->get['filter_quantity'])) {
								$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
							}

							if (isset($this->request->get['filter_status'])) {
								$url .= '&filter_status=' . $this->request->get['filter_status'];
							}

							if (isset($this->request->get['sort'])) {
								$url .= '&sort=' . $this->request->get['sort'];
							}

							if (isset($this->request->get['order'])) {
								$url .= '&order=' . $this->request->get['order'];
							}

							if (isset($this->request->get['page'])) {
								$url .= '&page=' . $this->request->get['page'];
							}
		}
    	function upload_trailer_files($file, $id)
    	{
				    $obj=$this->form_model;
			        $prod_id=$id;
			        $_FILES=$file;			
							$i=0;
							foreach($_FILES as $k=>$file) {
								if($k != "video") {
									$name = $prod_id.'_'.$file['name'];
									$size = $file['size'];
									$usr_dir = '../trailer/usr_'.(int)$_SESSION['user_id'];
									if(!is_dir($usr_dir))
									mkdir($usr_dir, 0777, true);
									
									$new_dir = 'prod_'.$prod_id;
									$original_path = $usr_dir.'/'.$new_dir;
									if(!is_dir($original_path))
									mkdir($original_path, 0777, true);
									
									$tmp = $file['tmp_name'];
								
									if(move_uploaded_file($tmp, $original_path.'/'.$name))
									{
										  $videoname['ajax_trailer']=$name;
										 
										  $obj->updated($videoname,"id=".$id,'product');
									  if ($i == 1)break;
									   }
									
									 }					
								}
								$url = '';

							if (isset($this->request->get['filter_name'])) {
								$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
							}

							if (isset($this->request->get['filter_model'])) {
								$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
							}

							if (isset($this->request->get['filter_price'])) {
								$url .= '&filter_price=' . $this->request->get['filter_price'];
							}

							if (isset($this->request->get['filter_quantity'])) {
								$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
							}

							if (isset($this->request->get['filter_status'])) {
								$url .= '&filter_status=' . $this->request->get['filter_status'];
							}

							if (isset($this->request->get['sort'])) {
								$url .= '&sort=' . $this->request->get['sort'];
							}

							if (isset($this->request->get['order'])) {
								$url .= '&order=' . $this->request->get['order'];
							}

							if (isset($this->request->get['page'])) {
								$url .= '&page=' . $this->request->get['page'];
							}
		}
		
    	   function uploaded_files($file,$prod_id='')
	    {

			 $tmp_name = $prod_id.'_'.$file['name'];
			 $size = $file['size'];
			 echo $usr_dir = '../products/usr_'.(int)$_SESSION['user_id'];
			 if(!is_dir($usr_dir))
			 mkdir($usr_dir, 0777, true);
			
			 $new_dir = 'prod_'.$prod_id;
			 $original_path = $usr_dir.'/'.$new_dir;
			 if(!is_dir($original_path))
			 mkdir($original_path, 0777, true);
			
			 $tmp = $file['tmp_name'];
		     $tmp_name1 = $file["tmp_name"];
       		 $name = date("YmdHis").$file["name"];
       		 if(move_uploaded_file($tmp_name1, $original_path.'/'.$tmp_name))
       		 {
				$image = $this->resize1($tmp_name, 220, 220, $prod_id, 'thumb3', 'h');								
       		 	return $tmp_name;
       		 }
       		 return '';
	}
		public function resize1($filename, $width, $height, $prod_id, $thumb, $type) {
		/*if (!file_exists(DIR_IMG .'usr_'.(int)$_SESSION['user_id'].'/prod_'.$prod_id.'/'. $filename) || !is_file(DIR_IMG .'usr_'.(int)$_SESSION['user_id'].'/prod_'.$prod_id.'/'. $filename)) {
			return;
		} */
		
		$info = pathinfo($filename);
		
		$extension = $info['extension'];
		
		$old_image = 'usr_'.(int)$_SESSION['user_id'].'/prod_'.$prod_id.'/'.$filename;
		$new_image = 'usr_'.(int)$_SESSION['user_id'].'/prod_'.$prod_id.'/'.$thumb.'/'.$filename;
		if (!file_exists(DIR_IMG . $new_image) || (filemtime(DIR_IMG . $old_image) > filemtime(DIR_IMG . $new_image))) {
			$path = '';
			
			$directories = explode('/', dirname(str_replace('../', '', $new_image)));
			
			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;
				
				if (!file_exists(DIR_IMG . $path)) {
					@mkdir(DIR_IMG . $path, 0777);
				}		
			}

			list($width_orig, $height_orig) = getimagesize(DIR_IMG . $old_image);
			$file=DIR_IMG . $old_image;
			if (file_exists($file)) {
			$this->file = $file;

			$info = getimagesize($file);

			$this->info = array(
            	'width'  => $info[0],
            	'height' => $info[1],
            	'bits'   => $info['bits'],
            	'mime'   => $info['mime']
        	);
        	
        	$this->image = $this->create($file);
    	} else {
      		exit('Error: Could not load image ' . $file . '!');
    	}
			if ($width_orig != $width || $height_orig != $height) {
				
				if($type == 'w')
					$this->resize($width, $height, 'w' );
				else
					$this->resize($width, $height, 'h' );
					
				$this->save(DIR_IMG . $new_image);
				
			} else {
				copy(DIR_IMG . $old_image, DIR_IMG . $new_image);
			}
		}
		
		if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
			return 'products/' . $old_image;
		} else {
			return 'products/' . $old_image;
		}	
	}
	private function create($image) {
		$mime = $this->info['mime'];
		
		if ($mime == 'image/gif') {
			return imagecreatefromgif($image);
		} elseif ($mime == 'image/png') {
			return imagecreatefrompng($image);
		} elseif ($mime == 'image/jpeg') {
			return imagecreatefromjpeg($image);
		}
    }	
	public function save($file, $quality = 90) {
		$info = pathinfo($file);
       
		$extension = strtolower($info['extension']);
   		
		if (is_resource($this->image)) {
			if ($extension == 'jpeg' || $extension == 'jpg') {
				imagejpeg($this->image, $file, $quality);
			} elseif($extension == 'png') {
				imagepng($this->image, $file);
			} elseif($extension == 'gif') {
				imagegif($this->image, $file);
			}
			   
			imagedestroy($this->image);
		}
    }
    public function resize($width = 0, $height = 0, $default = '') {
    	if (!$this->info['width'] || !$this->info['height']) {
			return;
		}

		$xpos = 0;
		$ypos = 0;
		$scale = 1;

		$scale_w = $width / $this->info['width'];
		$scale_h = $height / $this->info['height'];

		if ($default == 'w') {
			$scale = $scale_w;
		} elseif ($default == 'h'){
			$scale = $scale_h;
		} else {
			$scale = min($scale_w, $scale_h);
		}

		if ($scale == 1 && $scale_h == $scale_w && $this->info['mime'] != 'image/png') {
			return;
		}

		$new_width = (int)($this->info['width'] * $scale);
		$new_height = (int)($this->info['height'] * $scale);			
    	$xpos = (int)(($width - $new_width) / 2);
   		$ypos = (int)(($height - $new_height) / 2);
        		        
       	$image_old = $this->image;
        $this->image = imagecreatetruecolor($width, $height);
			echo $this->image;
		if (isset($this->info['mime']) && $this->info['mime'] == 'image/png') {		
			imagealphablending($this->image, false);
			imagesavealpha($this->image, true);
			$background = imagecolorallocatealpha($this->image, 255, 255, 255, 127);
			imagecolortransparent($this->image, $background);
		} else {
			$background = imagecolorallocate($this->image, 255, 255, 255);
		}
		
		imagefilledrectangle($this->image, 0, 0, $width, $height, $background);
	
        imagecopyresampled($this->image, $image_old, $xpos, $ypos, 0, 0, $new_width, $new_height, $this->info['width'], $this->info['height']);
        imagedestroy($image_old);
           
        $this->info['width']  = $width;
        $this->info['height'] = $height;
    }

/*
 * It is not reccomended to change the following class until you know what are you doing
 * For customizing javascript settings, ovveriding them from here
 */

//=============================== Upload Class =================================================\\
//class RealAjaxUploader
//{
	public $file_name 	= '';
	public $file_size 	= 0;
	public $upload_path = 'uploads/';
	public $temp_path 	= 'temp/';
	public $allow_ext 		= array();
	public $max_file_size 	= '10240M';
	
	public $override = false;
	public $deny_ext = array();
	public $upload_errors = array(
		UPLOAD_ERR_OK        	=> "No errors.",
		UPLOAD_ERR_INI_SIZE    	=> "The uploaded file exceeds the upload_max_filesize directive in php.ini",
		UPLOAD_ERR_FORM_SIZE    => "Larger than form MAX_FILE_SIZE.",
		UPLOAD_ERR_PARTIAL   	=> "Partial upload.",
		UPLOAD_ERR_NO_FILE      => "No file.",
		UPLOAD_ERR_NO_TMP_DIR   => "No temporary directory.",
		UPLOAD_ERR_CANT_WRITE   => "Can't write to disk.",
		UPLOAD_ERR_EXTENSION    => "File upload stopped by extension."
	);
	
	public $mail_receiver = '';
	public $finish_function ='';
	
	public $cross_origin = false;
	//function __construct($deny_ext=array())
	function construct($deny_ext=array())
	{
		//set data from JAVASCRIPT
		if(isset($_REQUEST['ax-max-file-size'])) 	$this->setMaxFileSize($_REQUEST['ax-max-file-size']);
		if(isset($_REQUEST['ax-file-path']))	 	$this->setUploadPath($_REQUEST['ax-file-path']);
		if(isset($_REQUEST['ax-allow-ext']))		$this->setAllowExt( !empty($_REQUEST['ax-allow-ext']) ? explode('|', $_REQUEST['ax-allow-ext']): array() );
		if(isset($_REQUEST['ax-override']))			$this->setOverride(true);
		//set deny
		$this->deny_ext = $deny_ext;
		
		//active parameters neccessary for upload
		$this->file_name = isset($_REQUEST['ax-file-name']) ? $_REQUEST['ax-file-name']:$_FILES['ax_file_input']['name'];
		$this->file_size = isset($_REQUEST['ax-file-size']) ? $_REQUEST['ax-file-size']:$_FILES['ax_file_input']['size'];

		//create a temp folder for uploading the chunks
		$ini_val = @ini_get('upload_tmp_dir');
		$this->temp_path = $ini_val ? $ini_val : sys_get_temp_dir();
		$this->temp_path = $this->temp_path.DIRECTORY_SEPARATOR;
		//$this->makeDir($this->temp_path);
	}
	
	/**
	 * Set the maximum file size, expected string with byte notation
	 * @param string $max_file_size
	 */
	public function setMaxFileSize($max_file_size = '10M')
	{
		$this->max_file_size = $max_file_size;
	}
	
	/**
	 * Set the allow extension file to upload
	 * @param array $allow_ext
	 */
	public function setAllowExt($allow_ext=array())
	{
		$this->allow_ext = $allow_ext;
	}
	
	/**
	 * Set the upload poath as string
	 * @param string $upload_path
	 */
	public function setUploadPath($upload_path)
	{
		$upload_path = rtrim($upload_path, '\\/');
		$this->upload_path = $upload_path.DIRECTORY_SEPARATOR;
		// Create thumb path if do not exits
		$this->makeDir($this->upload_path);
	}
	public function setOverride($bool){
		$this->override=$bool;	
	}
	
	private function makeDir($dir)
	{
		// Create thumb path if do not exits
		if(!file_exists($dir) && !empty($dir))
		{
			$done = @mkdir($dir, 0777, true);
			if(!$done)
			{
				$this->message(-1, 'Cannot create upload folder');
			}
		}
	}
	
	//Create a image thumb
	private function createThumbGD($quality=75)
	{
		//Settings for thumbnail generation, can be changed here or from js
		$maxheight	= isset($_REQUEST['ax-thumbHeight'])?$_REQUEST['ax-thumbHeight']:0;
		$maxwidth	= isset($_REQUEST['ax-thumbWidth'])?$_REQUEST['ax-thumbWidth']:0;
		$postfix	= isset($_REQUEST['ax-thumbPostfix'])?$_REQUEST['ax-thumbPostfix']:'_thumb';
		$thumb_path	= isset($_REQUEST['ax-thumbPath'])?$_REQUEST['ax-thumbPath']:'';
		$format		= isset($_REQUEST['ax-thumbFormat'])?$_REQUEST['ax-thumbFormat']:'png';
		
		$filepath = $this->upload_path.$this->file_name;
		
		if( ($maxwidth<=0 && $maxheight<=0) || !is_numeric($maxwidth) || !is_numeric($maxheight) )
		{
			return 'No valid width and height given';
		}
	
		$web_formats= array('jpg','jpeg','png','gif');//web formats
		$file_name	= pathinfo($filepath);
		if(empty($format)) $format = $file_name['extension'];
	
		if(!in_array(strtolower($file_name['extension']), $web_formats))
		{
			return 'Not supported file type';
		}
	
		$thumb_name	= $file_name['filename'].$postfix.'.'.$format;//filename 5.2++
	
		if(empty($thumb_path))	$thumb_path=$file_name['dirname'];
		
		$thumb_path.= (!in_array(substr($thumb_path, -1), array('\\','/') ) )?DIRECTORY_SEPARATOR:'';//normalize path
	
		if(!file_exists($thumb_path) && !empty($thumb_path))
		{
			@mkdir($thumb_path, 0777, true);
		}
		
		// Get new dimensions
		list($width_orig, $height_orig) = getimagesize($filepath);
		if($width_orig>0 && $height_orig>0)
		{
			$ratioX	= $maxwidth/$width_orig;
			$ratioY	= $maxheight/$height_orig;
			$ratio 	= min($ratioX, $ratioY);
			$ratio	= ($ratio==0)?max($ratioX, $ratioY):$ratio;
			$newW	= $width_orig*$ratio;
			$newH	= $height_orig*$ratio;
				
			// Resample
			$thumb = imagecreatetruecolor($newW, $newH);
			$image = imagecreatefromstring(file_get_contents($filepath));
				
			imagecopyresampled($thumb, $image, 0, 0, 0, 0, $newW, $newH, $width_orig, $height_orig);
	
			// Output
			switch (strtolower($format)) {
				case 'png':
					imagepng($thumb, $thumb_path.$thumb_name, 9);
					break;
						
				case 'gif':
					imagegif($thumb, $thumb_path.$thumb_name);
					break;
						
				default:
					imagejpeg($thumb, $thumb_path.$thumb_name, $quality);;
					break;
			}
			imagedestroy($image);
			imagedestroy($thumb);
		}
		else
		{
			return false;
		}
	}
		private function checkSize()
	{
		//------------------max file size check from js
		$max_file_size = $this->max_file_size;
		$size = $this->file_size;
		$rang 		= substr($max_file_size,-1);
		$max_size 	= !is_numeric($rang) && !is_numeric($max_file_size)? str_replace($rang, '', $max_file_size): $max_file_size;
		if($rang && $max_size)
		{
			switch (strtoupper($rang))//1024 or 1000??
			{
				case 'Y': $max_size = $max_size*1024;//Yotta byte, will arrive such day???
				case 'Z': $max_size = $max_size*1024;
				case 'E': $max_size = $max_size*1024;
				case 'P': $max_size = $max_size*1024;
				case 'T': $max_size = $max_size*1024;
				case 'G': $max_size = $max_size*1024;
				case 'M': $max_size = $max_size*1024;
				case 'K': $max_size = $max_size*1024;
			}
		}
	
		if(!empty($max_file_size) && $size>$max_size)
		{
			return false;
		}
		//-----------------End max file size check
	
		return true;
	}
		private function checkName()
	{
		//comment if not using windows web server
		$windowsReserved	= array('CON', 'PRN', 'AUX', 'NUL','COM1', 'COM2', 'COM3', 'COM4', 'COM5', 'COM6', 'COM7', 'COM8', 'COM9',
				'LPT1', 'LPT2', 'LPT3', 'LPT4', 'LPT5', 'LPT6', 'LPT7', 'LPT8', 'LPT9');
		$badWinChars		= array_merge(array_map('chr', range(0,31)), array("<", ">", ":", '"', "/", "\\", "|", "?", "*"));
	
		$this->file_name	= str_replace($badWinChars, '', $this->file_name);
	
		//check if legal windows file name
		if(in_array($this->file_name, $windowsReserved))
		{
			return false;
		}
		return true;
	}
	
	/**
	 * Check if a file exits or not and calculates a new name for not oovverring other files
	 * @param string $upload_path
	 */
	private function checkFileExits($upload_path='')
	{
		if($upload_path=='') $upload_path = $this->upload_path;
		if(!$this->override)
		{
			usleep(rand(100, 900));
			
			$filename 		= $this->file_name;
			//$upload_path 	= $this->upload_path;
			
			$file_data 	= pathinfo($filename);
			$file_base	= $file_data['filename'];
			$file_ext	= $file_data['extension'];//PHP 5.2>
		
			//Disable this lines of code to allow file override
			$c=0;
			while(file_exists($upload_path.$filename))
			{
				$find = preg_match('/\((.*?)\)/', $filename, $match);
				if(!$find) $match[1] = 0;
				else
					$file_base = str_replace("(".$match[1].")", "", $file_base);
					
				$match[1]++;
		
				$filename	= $file_base."(".$match[1].").".$file_ext;
			}
			// end
			$this->file_name = $filename;
		}
	}
	
	public function _checkFileExists()
	{
		$filename 		= $this->file_name;
		$upload_path 	= $this->upload_path;
		return file_exists($upload_path.$filename);
	}
	
	public function deleteFile(){
		$del = @unlink($this->upload_path.$this->file_name);
		return $del;
	}
	
	//Check if file type is allowed for upload
	private function checkExt()
	{
		$file_ext = strtolower( pathinfo($this->file_name, PATHINFO_EXTENSION) );
		
		//extensions not allowed for security reason and check if is allowed extension
		if(in_array($file_ext, $this->deny_ext)  || (!in_array($file_ext, $this->allow_ext) && count($this->allow_ext)) )
		{
			return false;
		}
		return true;
	}
	
	// Simle email sender function
	public function setEmail($main_receiver, $from='ajax@uploader')
	{
		$this->mail_receiver 	= $main_receiver;
		$this->mail_cc 			= '';
		$this->mail_from 		= $from ? $from : 'ajax@uploader';
	}
	
	private function sendEmail()
	{
		if($this->mail_receiver)
		{
			$msg = '<p> New file uploaded to your site at '.date('Y-m-i H:i'). ' from IP '.$_SERVER['REMOTE_ADDR'].':</p>';
			$msg.= '<div style="overflow:auto;padding:10px;border:1px solid black;border-radius:5px;">';
			$msg.= $this->upload_path.$this->file_name;
			$msg.= '</div>';
			$headers = 'From: '.$this->mail_from. "\r\n" .'Reply-To: '.$this->mail_from. "\r\n" ;
			$headers .= 'Cc: '.$this->mail_cc  . "\r\n";
			$headers .= "Content-type: text/html\r\n";
		
			@mail($this->mail_receiver, 'New file uploaded', $msg, $headers);
		}
	}

	private function uploadAjax()
	{
		$currByte	= isset($_REQUEST['ax-start-byte'])?$_REQUEST['ax-start-byte']:0;
		$isLast		= isset($_REQUEST['ax-last-chunk'])?$_REQUEST['ax-last-chunk']:'true';
		
		$flag = FILE_APPEND;
		if($currByte==0)
		{
			$this->checkFileExits($this->temp_path);//check if file exits in temp path, not so neccessary
			$flag = 0;
		}
		
		//we get the path only for the first chunk
		$full_path 	= $this->temp_path.$this->file_name;

		//formData post files just normal upload in $_FILES, older ajax upload post it in input
		$post_bytes	= file_get_contents( isset($_FILES['ax_file_input']) ? $_FILES['ax_file_input']['tmp_name'] : 'php://input' );
		
		//some rare times (on very very fast connection), file_put_contents will be unable to write on the file, so we try until it writes
		$try = 20;
		while(@file_put_contents($full_path, $post_bytes, $flag) === false && $try>0)
		{
			usleep(50);
			$try--;
		}
		
		if(!$try)
		{
			$this->message(-1, 'Cannot write on file.');
		}
		
		//delete the temporany chunk
		if(isset($_FILES['ax_file_input']))
		{
			@unlink($_FILES['ax_file_input']['tmp_name']);
		}
		
		//if it is not the last chunk just return success chunk upload
		if($isLast!='true')
		{
			$this->message(1, 'Chunk uploaded');
		}
		else
		{
			$this->checkFileExits($this->upload_path);
			
			/*$i = strrpos($this->file_name,".");
			if ($i) {
				$l = strlen($this->file_name) - $i;
				$ext = substr($this->file_name,$i+1,$l);
			}
			$this->file_name = mt_rand().time().".".$ext;*/
			
			$ret = rename($full_path, $this->upload_path.$this->file_name);//move file from temp dir to upload dir TODO this can be slow on big files and diffrent drivers
			if($ret)
			{
				$extra_info = $this->finish();
				$this->message(1, 'File uploaded', $extra_info);
			}
			else
			{
				$this->message(1, 'File move error', $extra_info);
			}
		}
	}
	private function uploadStandard()
	{
		$this->checkFileExits($this->upload_path);
		$full_path 	= $this->upload_path.$this->file_name;
		$result 	= move_uploaded_file($_FILES['ax_file_input']['tmp_name'], $full_path);//make the upload
		if(!$result) //if any error return the error
		{
			$this->message(-1, 'File move error');
		}
		else
		{
			$extra_info = $this->finish();
			$this->message(1, 'File uploaded', $extra_info);
		}
	}
	
	public function uploadFile()
	{
		if($this->checkFile())//this checks every chunk FIXME is right?
		{
			$is_ajax	= isset($_REQUEST['ax-last-chunk']) && isset($_REQUEST['ax-start-byte']);
			if($is_ajax)//Ajax Upload, FormData Upload and FF3.6 php://input upload
			{
				$this->uploadAjax();
			}
			else //Normal html and flash upload
			{
				$this->uploadStandard();
			}
		}
	}
	
	private function finish()
	{
		ob_start();
		//create a thumb if data is set
		$this->createThumbGD(100);
		
		//send a notification if is set
		$this->sendEmail();
		
		//run the external user success function
		if($this->finish_function && function_exists( $this->finish_function ))
		{
			try {
				call_user_func($this->finish_function, $this->upload_path.$this->file_name);
			} catch (Exception $e) {
				echo $e->getTraceAsString();
			}
			
		}
		$value = ob_get_contents();
		ob_end_clean();
		return $value;
	}
	
	private function checkFile()
	{
		//check uploads error
		if(isset($_FILES['ax_file_input']))
		{
			if( (string)$_FILES['ax_file_input']['error'] !== (string)UPLOAD_ERR_OK )
			{
				$this->message(-1, $this->upload_errors[$_FILES['ax_file_input']['error']]);
				
			}
		}
		//check ext
		$allow_ext = $this->checkExt();
		
		if(!$allow_ext)
		{
			$this->message(-1, 'File extension is not allowed');
		}

		//check name
		$fn_ok = $this->checkName();
		if(!$fn_ok)
		{
			$this->message(-1, 'File name is not allowed. System reserved.');
		}

		//check size
		if(!$this->checkSize())
		{
			$this->message(-1, 'File size exceeded maximum allowed: '.$this->max_file_size);
		}
		return true;
	}
	
	public function header()
	{
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
		header('X-Content-Type-Options: nosniff');
		if ($this->cross_origin) 
		{
			header('Access-Control-Allow-Origin: *');
        	header('Access-Control-Allow-Credentials: false');
        	header('Access-Control-Allow-Methods: OPTIONS, HEAD, GET, POST, PUT, PATCH, DELETE');
        	header('Access-Control-Allow-Headers: Content-Type, Content-Range, Content-Disposition');
		}
	}
	
	private function message($status, $msg, $extra_info='')
	{
		$this->header();
		echo json_encode(array('name'=>$this->file_name, 'size'=>$this->file_size, 'status'=>$status,'info'=>$msg, 'more'=>$extra_info));
		die();
	}
	
	public function onFinish($fun){
		$this->finish_function = $fun;
	}
}
