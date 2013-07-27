<?php
class LineAction extends Action{
/*************authorize**************/
	private function authorize(){
		$uid=$_REQUEST['uid'];
		$token=$_REQUEST['token'];
		if(is_null($uid)||is_null($token)){//空，认为没有该账户
			$this->ajaxReturn(null,"addLine FAILED:authorize is WRONG ",109);
		}
		$User=new UserModel();
		$result=$User->authorize($uid,$token);
		if(!$result){
			$this->ajaxReturn(null,"addLine FAILED:authorize is WRONG ",109);
		}
	}

/**************line*****************/
	/**
	*param 
	*/
	public function friends_lines(){
		$this->authorize();
		//
		$uid=$_REQUEST['uid'];
		$firstid=$_REQUEST['firstid'];
		$lastid=$_REQUEST['lastid'];
		$flag=$_REQUEST['flag'];
		if(is_null($uid)||is_null($firstid)||is_null($lastid)){
		    $this->ajaxReturn(null,"get friends_lines FAILED:the parameters cannot be null !",601);
		}
		if(is_null($flag)){
		    $flag=LineModel::FLAG_REFRESH;
		}
		$Line =new LineModel();
		$result=$Line->getFriendsLines($uid,$firstid,$lastid,$flag);
		if(is_null($result)){
		    $this->ajaxReturn(null,'get friends_lines FAILED:query error !',601);
		}else{
		    $this->ajaxReturn($result,'get friends_lines SUCCESS',602);
		}
	}
	
	public function addline(){
		$this->authorize();
		//
		if(is_null($_REQUEST['clipid'])){
			$this->ajaxReturn(null,"the line's clip cannot be NULL",302);
		}
		$data=array();
		$data['uid']=$_REQUEST['uid'];
	//	$data['cateid']=$_REQUEST['cateid'];
		$data['clipid']=$_REQUEST['clipid'];
		$data['content']=$_REQUEST['content'];
		$data['birth']=date('Y-m-d H:i:s',time());
		//
		//这里要对content的长度做判断，不能插入太长的内容
		//此处只是简单的字节长度判断，不够准确，应该对字符串分中英文分别判断长度
		//
		if(strlen($_REQUEST['content'])>1000){
			$this->ajaxReturn(null,'the line content is too long ',301);
		}
		$Line=new LineModel();
		$result=$Line->add($data);
		if($result==null||!$result){
			$this->ajaxReturn(null,'addline FAILED',302);
		}else{
			$this->ajaxReturn($result,'add line SUCCESS',303);
		}
	}

	public function delline(){
		$this->authorize();
		//
		$lineid=$_POST['lineid'];
		if(is_null($lineid)){
			$this->ajaxReturn(null,"delete line FAILED:the line id is NULL ",304);
		}
		$Line=new LineModel();
		$condition['id']=$lineid;
		$result=$Line->where($condition)->delete();
		if($result==null||!$result){
			$this->ajaxReturn(null,"delete line FAILED",304);
		}else{
			$this->ajaxReturn($result,"delete line SUCESS",305);
		}
	}

	public function updateline(){
		$this->authorize();
	}

/*****************category*************/
	/**
	*获取一个用户所有的clip
	*param uid 用户id
	*param token 验证的token
	*/
	public function categories(){
		$Cate=M("Category");
		$sql="SELECT * FROM lips_category ORDER BY listorder";
		$result=$Cate->query($sql);
		if ($result===false || $result==null) {
	      	$this->ajaxReturn($result,"get categories FAILED",401);
	    }else{
			$this->ajaxReturn($result,"get categories SUCESS ",402);
		}
	}
	/**
	*获取一个用户所有的clip
	*param uid 用户id
	*/
	public function clips(){
		$Clip=M("Clip");
		$sql="SELECT * FROM lips_clip WHERE uid=".$_REQUEST['uid'];
		$result=$Clip->query($sql);
		if($result==false||$result==null){
			$this->ajaxReturn($result,"get clips FAILED",501);
		}else{
			$this->ajaxReturn($result,"get categories SUCESS ",502);
		}
	}
}
?>