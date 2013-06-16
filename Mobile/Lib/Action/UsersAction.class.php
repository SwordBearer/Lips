<?php
class UsersAction extends Action {
/********************authorize*************/
	private function authorize(){
		$uid=$_REQUEST['uid'];
		$token=$_REQUEST['token'];
		if(is_null($uid)||is_null($token)){//空，认为没有该账户
			$this->ajaxReturn(null,"addLine FAILED:authorize is WRONG ",109);
		}
		$User=new UserModel();
		$result=$User->authorize($uid,$token);
		if(!$result){//User中需要对105重新处理
			$this->ajaxReturn(null,"addLine FAILED:authorize is WRONG ",109);
		}
	}
/*************客户端接口********************/
	public function login(){
		$email=$_REQUEST['email'];
		$pwd=md5($_REQUEST['pwd']);
		if(is_null($email)||is_null($pwd)){
			$this->ajaxReturn(null,"login FAILED:the email and password cannot be null !",104);
		}
		$User=new UserModel();
		$sql="SELECT id,pwd,token FROM lips_user WHERE email='".$email."'";
		$tmp=$User->query($sql);
		if ($tmp===false || $tmp==null) {
	      	$this->ajaxReturn($tmp,"login FAILED:there isn't this user",104);
	    }else{
	    	$result=$tmp[0];
	    	if($result['pwd']!=$pwd){//如果密码不符合
	    		$this->ajaxReturn($result,"login FAILED:password is wrong",105);
	    	}else{
	    		if($result['token']==NULL||$result['token']==''){//保存token
	    			$result['token']=$User->generateToken($result['id'],$result['uname'],$result['pwd']);
	    		}
	    		$result['pwd']=NULL;
	    		$this->ajaxReturn($result,'login SUCCESS',106);
	    	}
	    }
	}
	//获取用户的简要信息,需要认证
	public function briefinfo(){
		$this->authorize();

		$User=new UserModel();
	    $result=$User->getBriefInfo($_POST['uid']);
	    if ($result===false || $result==null) {
	    	$this->ajaxReturn($result,"getUserInfo FAILED",107);
	    }else{
	    	$this->ajaxReturn($result,"getUserInfo SUCCESS",108);
	    }
	}
//需要认证
	public function news(){
		$this->authorize();

		$User=new UserModel();
	  	$news=$User->getNews($_REQUEST['uid']);
	    if($news==null||!$news){
	   		$this->ajaxReturn(null,"getNews FAILED:there isn't this user",201);
	   	}else{
	    	$this->ajaxReturn($news,"getNews SUCCESS",202);
	   	}
	}

	public function signup(){
		$uname=$_POST['uname'];
		$udesc=$_POST['udesc'];
		$email=$_POST['email'];
		$pwd=$_POST['pwd'];
		$gender=$_POST['gender'];
		$type=$_POST['type'];
		//验证参数
		if(is_null($uname)||is_null($email)||is_null($pwd)||is_nan($type)||is_null($gender)){
			$this->ajaxReturn(null,"sign up FAILED:the value should't be NULL",102);
			return;
		}
		if(strlen($uname)<3||strlen($uname)>30){
			$this->ajaxReturn(null,"sign up FAILED:username length is valid",102);
		}
		if(strlen($udesc)>600){
			$this->ajaxReturn(null,"sign up FAILED:username length is valid",102);
		}
		if(strlen($email)<7||strlen($email)>30){
			$this->ajaxReturn(null,"sign up FAILED:email length is valid",102);
		}
		if(strlen($pwd)<4||strlen($pwd)>20){
			$this->ajaxReturn(null,"sign up FAILED:password length is valid",102);
		}
		$User =new UserModel();
		$condition['email']=$email;
		$result=$User->where($condition)->find();
		if($result){//该用户已经存在了，注册失败
			$this->ajaxReturn(null,'此邮箱已经被注册',101);
		}else{
			$data=array();
			$data['uname']=$uname;
			$data['udesc']=$udesc;
			$data['email']=$email;
			$data['pwd']=md5($pwd);
			$data['gender']=$gender;
			$data['type']=$type;
			$data['birth']=date('Y-m-d',time());
			$result=$User->add($data);
			if($result){
				$this->ajaxReturn($result,'sign up SUCCESS',103);
			}else{
				$this->ajaxReturn(null,'sign up FAILED',102);
			}
		}
	}
}

?>