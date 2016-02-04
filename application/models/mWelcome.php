<?php
class MWelcome extends CI_Model{

public function __construct()
	{
		$this->load->database();
	}
/*
|-----------------------------------------------------------------------------------
|登录
|-----------------------------------------------------------------------------------
*/
//用户登录验证
public function mDoLogin($mail,$password)
{
	$result=$this->db->get_where('user',array('mail'=>$mail,'password'=>$password,'state'=>1));
	$data = $result->result_array();
	$num=count($data);
	if($num==1)
	{
		$userId = $data[0]['userId'];
		$nickName = $data[0]['nickName'];
		$this->session->set_userdata('freesite_userId',$userId);
		$this->session->set_userdata('freesite_nickName',$nickName);
		$this->session->set_userdata('freesite_mail',$mail);
		return true;
	}
	else
	{
		return false;
	}
}
//用户生成session
	public function m_create_session($username,$password)
	{
		$result=$this->db->get_where('user',array('mail'=>$username));
		$userpassword=$result->result_array();
		$mypassword='';
		$length=count($userpassword);
		for($i=0;$i<$length;$i++)
		{
			$mypassword=$userpassword[$i]['password'];
		}
		if($mypassword==$password)
		{
			$this->session->set_userdata('freesite_mail',$username);
			return 'true';
		}
		else
		{
			return 'wrong';
		}
	}
//获取session
	public function m_get_user()
	{
		$result=$this->session->userdata('freesite_mail');
		return $result;
	}

//注册用户
public function mDoRegiste($mail,$password,$username,$check,$state)
{
	$result=$this->db->get_where('user',array('mail'=>$mail));
	$num=count($result->result_array());
	if($num==0)
	{
		$result2=$this->db->insert('user',array('mail'=>$mail,'password'=>$password,'nickName'=>$username,'check'=>$check,'state'=>$state));
		if($result2)
		{
			return 'right';
		}
		else{
			return 'wrong';
		}
	}
	else
	{
		return 'has';
	}
}
//验证邮箱
public function mCheckMail($mail,$check)
{
	$result=$this->db->get_where('user',array('mail'=>$mail,'check'=>$check));
	$num=count($result->result_array());
	if($num==1)
	{
		$this->db->where(array('mail'=>$mail));
		$result2 = $this->db->update('user',array('state'=>1));
		if($result2)
		{
			$result3 = $this->db->get_where('user',array('mail'=>$mail));
			$data = $result3->result_array();
			if(count($data)>0)
			{
				//为新注册用户添加模块
				$userId = $data[0]['userId'];
				$part = array(
					array('userId'=>$userId,'title'=>'常用','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>1),
					array('userId'=>$userId,'title'=>'性能优化','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>2),
					array('userId'=>$userId,'title'=>'CSS3','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>3),
					array('userId'=>$userId,'title'=>'HTML5','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>4),
					array('userId'=>$userId,'title'=>'设计','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>5),
					array('userId'=>$userId,'title'=>'鸡汤','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>6),
					array('userId'=>$userId,'title'=>'音乐','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>7),
					array('userId'=>$userId,'title'=>'个人博客','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>8),
					array('userId'=>$userId,'title'=>'个人作品','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>9),
					array('userId'=>$userId,'title'=>'消遣','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>10),
					array('userId'=>$userId,'title'=>'Web前端','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>11),
					array('userId'=>$userId,'title'=>'时尚','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>12),
					array('userId'=>$userId,'title'=>'生活常识','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>13),
					array('userId'=>$userId,'title'=>'PHP','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>14),
					array('userId'=>$userId,'title'=>'JavaScript','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>15),
					array('userId'=>$userId,'title'=>'个人修养','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>16),
				);
				for($i=0;$i<16;$i++)
				{
					$this->db->insert('parts',array('userId'=>$part[$i]['userId'],'title'=>$part[$i]['title'],'state'=>$part[$i]['state'],'private'=>$part[$i]['private'],'password'=>$part[$i]['password'],'order'=>$part[$i]['order'],'from'=>'','otherPassword'=>$part[$i]['otherPassword'],'sort'=>$part[$i]['sort']));
				}
				//修改模块内容来源为自己
				$result4 = $this->db->get_where('parts',array('userId'=>$userId));
				$result_array = $result4->result_array();
				for($i=0;$i<16;$i++)
				{	
					$this->db->where(array('partsId'=>$result_array[$i]['partsId']));
					$this->db->update('parts',array('from'=>$result_array[$i]['partsId']));
				}
			}
			return 'right';
		}
		else{
			return 'false';
		}
	}
	else{
		return 'wrong';
	}
}
//添加链接
public function mAddLinks($userId,$partsId,$linkName,$links)
{
	$result = $this->db->insert('links',array('userId'=>$userId,'partId'=>$partsId,'name'=>$linkName,'links'=>$links));
	if($result)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//删除链接
public function mDelLinks($userId,$id)
{
	//$this->db->where();
	$result = $this->db->delete('links',array('linksId'=>$id,'userId'=>$userId));
	if($result)
	{
		return true;
	}
	else
	{
		return false;
	}
}
//获取登录人的userId
public function mGetUserId($username)
{
	$result = $this->db->get_where('user',array('mail'=>$username));
	if($result!='')
	{
		return $result[0]['userId'];
	}
	else
	{
		return '';
	}
}
//获取模块
public function mGetParts()
{
	$userId=$this->session->userdata('freesite_userId');
	$result = $this->db->get_where('parts',array('userId'=>$userId,'state'=>1));
	return $result->result_array();
}
/**
*@获取链接
*
*
*/
public function mGetLinks($partsId)
{
	$userId=$this->session->userdata('freesite_userId');
	if($userId!='')
	{
		$result = $this->db->get_where('parts',array('partsId'=>$partsId));
		$result_array = $result->result_array();
		$from = $result_array[0]['from'];
		$otherPssword = $result_array[0]['otherPassword'];
		if($partsId==$from)
		{
			//是从自己这里订阅则不要密码
			$this->db->order_by('sort','ASC');
			$result = $this->db->get_where('links',array('partId'=>$partsId));
			return $result->result_array();
		}
		else
		{
			//从别人那里订阅则要验证密码
			$result = $this->db->get_where('parts',array('partsId'=>$from));
			$result_array = $result->result_array();
			$private = $result_array[0]['private'];
			$password = $result_array[0]['password'];
			if($private==0)
			{
				//不对外开放
				if($otherPassword==$password)
				{
					$this->db->order_by('sort','ASC');
					$result = $this->db->get_where('links',array('partId'=>$from));
					return $result->result_array();
				}
				else
				{
					return 'wrong';
				}
			}
			else
			{
				//对外开放
				$this->db->order_by('sort','ASC');
				$result = $this->db->get_where('links',array('partId'=>$from));
				return $result->result_array();
			}
		}
	}
}
//重排序链接
public function mResortLink($partId,$sort)
{
	$userId=$this->session->userdata('freesite_userId');
	if($userId!='')
	{
		$length = count($sort);
		for($i=0;$i<$length;$i++)
		{
			$this->db->where('linksId',$sort[$i]);
			$this->db->update('links',array('partId'=>$partId,'sort'=>$i));
		}
		return true;
	}
	else
	{
		return false;
	}
}
//获取所有文章
public function mGetArticle()
{
	$result = $this->db->get('articles');
	return $result->result_array();
}
//添加博客内容
public function mDoAddBlog($title,$content)
{
	$userId=$this->session->userdata('freesite_userId');
	$mail=$this->session->userdata('freesite_mail');
	if($userId!='')
	{
		$result = $this->db->insert('articles',array('userId'=>$userId,'mail'=>$mail,'title'=>$title,'content'=>$content));
		if($result)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
//QQ登录
public function mQQLogin($nickName,$unionId)
{
	$result = $this->db->get_where('user',array('unionId'=>$unionId));
	$result_array = $result->result_array();
	$length = count($result_array);
	if($length==0)
	{
		//添加此用户
		$result2 = $this->db->insert('user',array('nickName'=>$nickName,'unionId'=>$unionId,'state'=>1));
		$userId = $this->db->insert_id();
		if($result2)
		{
			$part = array(
				array('userId'=>$userId,'title'=>'常用','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>1),
				array('userId'=>$userId,'title'=>'性能优化','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>2),
				array('userId'=>$userId,'title'=>'CSS3','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>3),
				array('userId'=>$userId,'title'=>'HTML5','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>4),
				array('userId'=>$userId,'title'=>'设计','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>5),
				array('userId'=>$userId,'title'=>'鸡汤','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>6),
				array('userId'=>$userId,'title'=>'音乐','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>7),
				array('userId'=>$userId,'title'=>'个人博客','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>8),
				array('userId'=>$userId,'title'=>'个人作品','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>9),
				array('userId'=>$userId,'title'=>'消遣','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>10),
				array('userId'=>$userId,'title'=>'Web前端','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>11),
				array('userId'=>$userId,'title'=>'时尚','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>12),
				array('userId'=>$userId,'title'=>'生活常识','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>13),
				array('userId'=>$userId,'title'=>'PHP','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>14),
				array('userId'=>$userId,'title'=>'JavaScript','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>15),
				array('userId'=>$userId,'title'=>'个人修养','state'=>1,'private'=>1,'password'=>'','order'=>0,'from'=>'','otherPassword'=>'','sort'=>16),
			);
			for($i=0;$i<16;$i++)
			{
				$this->db->insert('parts',array('userId'=>$part[$i]['userId'],'title'=>$part[$i]['title'],'state'=>$part[$i]['state'],'private'=>$part[$i]['private'],'password'=>$part[$i]['password'],'order'=>$part[$i]['order'],'from'=>'','otherPassword'=>$part[$i]['otherPassword'],'sort'=>$part[$i]['sort']));
			}
			//修改模块内容来源为自己
			$result4 = $this->db->get_where('parts',array('userId'=>$userId));
			$result_array = $result4->result_array();
			for($i=0;$i<16;$i++)
			{	
				$this->db->where(array('partsId'=>$result_array[$i]['partsId']));
				$this->db->update('parts',array('from'=>$result_array[$i]['partsId']));
			}
			$this->session->set_userdata('freesite_userId',$userId);
			$this->session->set_userdata('freesite_nickName',$nickName);
			$this->session->set_userdata('freesite_unionId',$unionId);
			return true;
		}
		else
		{
			return false;
		}
	}
	else if($length==1)
	{
		//生成session
		$userId = $result_array[0]['userId'];
		$nickName = $result_array[0]['nickName'];
		$this->session->set_userdata('freesite_userId',$userId);
		$this->session->set_userdata('freesite_nickName',$nickName);
		$this->session->set_userdata('freesite_unionId',$unionId);
		return true;
	}
	else
	{
		return false;
	}
}
}
?>