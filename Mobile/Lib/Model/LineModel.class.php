<?php
class LineModel extends Model{
	CONST FLAG_REFRESH=1;
	CONST FLAG_MORE=2;
	public function getFriendsLines($uid,$firstid,$lastid,$flag){
		$pageSize=20;
		$sql1="SELECT lips_user.uname AS author,line.* FROM lips_line AS line,lips_user WHERE lips_user.id=line.uid ";
		$sql2="";
		$sql3=" AND (line.uid=".$uid." OR line.uid in ( SELECT lips_fans.usera FROM lips_fans,lips_user WHERE lips_fans.userb=".$uid." AND lips_user.id=".$uid.")) ORDER BY line.id DESC LIMIT ".$pageSize;;
		if($flag==$this::FLAG_REFRESH){
		    $sql2=" AND line.id>".$firstid;
		}else{//FLAG_MORE
		    $sql2=" AND line.id<".$lastid;
		}
		return $this->query($sql1.$sql2.$sql3);
	}
}
?>