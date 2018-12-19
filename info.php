<?php
//phpinfo();
?>
aba
<script>
var fullName="gopi kumar prg nath dhara";
var firstName = fullName.split(' ').slice(0, -1).join(' ');
var lastName = fullName.split(' ').slice(-1).join(' ');
console.log(firstName);
console.log(lastName);
</script>


==================
controller

/var/www/html/santu/internal/application/controllers/Tl_target_sheet.php

model

Mod_member_target_sheet model.php
Mod_monthly_target model.php

views

/var/www/html/santu/internal/application/views/tl_target_sheet/target_sheet_tl.php

/var/www/html/santu/internal/application/views/tl_target_sheet/target_sheet_particular.php

/var/www/html/santu/internal/application/views/tl_target_sheet/target_sheet.php
footer.php

if(document.documentElement.clientWidth<700)
 {
    alert("test");
    var script=document.createElement('script');
    script.type='text/javascript';
    script.src="//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js";
    
    
    document.body.appendChild(script);
 }
 
 
 if($_REQUEST['action']=='urlfriendly')
{
   $HTML .=<<<HTML
   <script type='text/javascript'>
 

//modified by gopi start

 if(screen.width < 700)
    {
        
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = "//ajax.googleapis.com/ajax/libs/jquerymobile/1.4.3/jquery.mobile.min.js";
        document.getElementsByTagName('head')[0].appendChild(script);
    }


    $(window).load(function(){
        if( $(window).width() < 700 )
            {
                $("#myCarousel").swiperight(function() {
                    $("#myCarousel").carousel('prev');  
                });  
                $("#myCarousel").swipeleft(function() {  
                    $("#myCarousel").carousel('next');  
                });
            }
    });
HTML;
}
			<button id="delwrongimg" type="button" class="close"  style="display: none;color: red;"><span aria-hidden="true">×</span></button> 
}

public function message_notification_web()
	{
		$user_id 	= $this->session->userdata('pro_login_id')!=''?$this->session->userdata('pro_login_id'):0;
		$user_type 	= $this->session->userdata('pro_user_type');
		$message_noti=$data_store=array();
		
		$table='project_message';
		$select = ' MAX(project_message.id) AS p_n_id';
		$where='(project.status = "Y" OR project.status = "A" ) AND applied_job.home_delete_status = "N" AND (applied_job.status = "Y" OR applied_job.status = "A" OR applied_job.status = "N") and project_message.receiver_id ='.$user_id.' AND project_message.sender_id!= "0"';
			
		$order_in = 'p_n_id';
		$order_by = 'desc';
		$from 		= '';
		$perpage 	= '';
		$join	= array(
							'applied_job' => 'project_message.apply_job_id = applied_job.id',
							'project' => 'project_message.project_id = project.id',
							);
		$group_by = 'project_message.apply_job_id';
		$message_noti_list=$this->sitesetting_model->global_fetchvalue($table,$select,$where,$order_in,$order_by,$from,$perpage,$join,$group_by);
		//echo $this->db->last_query();
		
		if(count($message_noti_list) > 0)
		{
			$last_names = implode(',',(array_column($message_noti_list, 'p_n_id')));
			$table='project_message';
			$select ='project_message.*,pros.contact_f_name,pros.contact_l_name,pros.profile_img,pros.com_name,pros.primary_category,pros.pros_avg_rating,pros.account_name,user.online_status';
			$where='project_message.web_notification ="0" AND project_message.id IN ('.$last_names.')';
					//$where='project.owner_user_id = '.$pro_login_id.' and project.id ='.$p_id;
			$from 		= '';
			$perpage 	= '';		
			$order_in = 'project_message.id';
			$order_by = 'desc';		
			$join	= array(
								'applied_job' => 'project_message.apply_job_id = applied_job.id',
								'project' => 'project_message.project_id = project.id',
								'pros' => 'applied_job.appliedby_id = pros.user_id',
								'user' => 'pros.user_id = user.id',
								
								);
			$group_by = '';
			$message_noti=$this->sitesetting_model->global_fetchvalue($table,$select,$where,$order_in,$order_by,$from,$perpage,$join,$group_by);
			//echo $this->db->last_query();
			if(count($message_noti)>0)
			{
				foreach($message_noti as $key => $val)
				{
					//web notification sent update
					$updateNoti= $this->common_model->update('project_message',array('web_notification'=>1),array('id'=>$val['id']));
					//for category image
					$category=$this->db->where(array('id'=>$val['primary_category']))->get('category')->result_array();
					$catimage = ( isset($category) && isset($category[0]['img']) ) ? $category[0]['img']:'';
					$cat=(isset($category[0]['category_name'])) ? ucwords($category[0]['category_name']):'';
					
					if((isset($val['profile_img']) && trim($val['profile_img'])!='' &&  file_exists('./assets/upload/users_profile_image/'.$val['profile_img'])== true ))
					{
					$profile_img = base_url().'assets/upload/users_profile_image/'.$val['profile_img'];
					}
					else
					{
					$profile_img = base_url().'assets/upload/category_image/'.$catimage;
					
					}
					
					if(trim($val['profile_img']) =='' && $catimage=='' )
					{
					$profile_img = base_url().'assets/upload/property_image/thumb/no-image.png';
					}
					
					$url_part = base_url().'account/project/messages/'.strtolower($val['account_name']).'/thread/'.$val['project_id'];
					
					$msg=$this->strip_html_tags1($val['message']);
					$replace   = array('\r\n', '\n', '\r', '&nbsp;');
					$replace_with = array(' ', ' ', ' ', ' ');
					$msg_display=str_replace($replace, $replace_with, $msg);
					$msg_display = (strlen($msg_display) > 100) ? substr($msg_display,0,100).'...' : $msg_display;
					
					$photo_file = isset($val['msg_attachment']) ? $val['msg_attachment'] : '';
					$other_file = isset($val['other_file']) ? $val['other_file'] : '';
					
					$text = '';
					if($photo_file!='')
					{
						$attach_type = explode('.',$photo_file) ;
						$extension_type = end($attach_type);
						if($extension_type == 'jpg' || $extension_type == 'jpeg' || $extension_type == 'png' || $extension_type == 'PNG' || $extension_type == 'JPG' || $extension_type == 'JPEG')
						{
							$text = 'Sent a photo';
						}
					}
					
					if($other_file!='')
					{
						$attach_type = explode('.',$other_file) ;
						$extension_type = end($attach_type);
						if($extension_type == 'docx' || $extension_type == 'pdf' || $extension_type == 'doc' || $extension_type == 'PDF' || $extension_type == 'DOC' || $extension_type == 'DOCX')
						{
							$text = 'Sent a file';
						}
					}
					
					$data_store[] = array(
								'id' =>$val['id'],
								'url_part'=>$url_part,
								'profile_image'=>$profile_img,
								'com_name' =>ucwords($val['com_name']),
								'msg' =>trim($msg_display),
								'msg_type' =>$val['message_type'],
								'push_text' => $text,
							);
					//echo "<pre>";print_r($data_store);die;
				}
			}
			
		}
		
		$msg_info['total_length'] =  count($message_noti);
		$msg_info['info'] =  $data_store;
		//echo json_encode($msg_info);
		//echo "<pre>";print
		 echo json_encode($msg_info);
	}


//					var content = data.split("###");
//					
//					if (content[2]>0) {
//                        
//						//$("#newindicator").show();
//						$("#newindicator").html('<span class="newMsg">new</span>');
//                    }
//					if (content[2]=='') {
//                        
//						//$("#newindicator").show();
//						$("#newindicator").html('');
//                    }
//					if ((content[3]>0) || (content[2]>0)) {
//						
//                       
//					  var sell_all='<a href="javascript:void(0)" class="filterAll all_project">See All <span><i class="fa fa-chevron-right" aria-hidden="true"></i></span></a>';
//					  $("#message_box").html('Messages'+sell_all);
//					   
//                    }
//					else
//					{
//						 $("#message_box").html('No active conversations');
//					}
//					
//					if (content[0].trim()!='') {
//                        $(".all_project").attr("href", "<?php echo base_url();?>account/project/messages/"+content[0]);
//                    }
//					
//					
//					$(".messageListnoti").html(content[4]);
					//var obj = JSON.parse(data.info);
					//console.log(obj);
					//var appdata='';
					//$.each(obj, function (key, val) {
					//
					//		alert(key + val);
					//		var clsname=val.classname;
					//		var usersimage=val.usersimage;
					//		var usertim=val.time;
					//		var urlpart=val.url_part;
					//		var msg_readstatus=val.msg_readstatus;
					//		if (msg_readstatus!='' && msg_readstatus==0) {
					//			
					//			var nw='<span class="newProHMsg">new </span>';
					//		}
					//		var Comnm=val.Comnm;
					//		var messageinfo=val.message_info;
					//		
					//		
					//			appdata+='<li class="'+clsname+'newMailIn"><a href="javascript:void(0)"><div class="logoUser" style="background: url('+usersimage+') no-repeat center bottom; background-size: auto;"></div><div class="messageNot"><div class="timestamp"><span>'+usertim+'</span></div><label style="cursor: pointer;" onclick="">'+nw+Comnm+'</label><p onclick="window.location.href ="><span class="attachF"><img src="<?php echo base_url();?>assets/images/mailAttach.jpg" alt="" /></span>'+messageinfo+'</p></div></a></li>'
					//});
					//
					//$(".messageListnoti").html(appdata);

?>
<input type="hidden" name="hdrmsglastId" id="hdrmsglastId" value="0">
<input type="hidden" name="hdlastprojectid" id="hdlastprojectid" value="0">