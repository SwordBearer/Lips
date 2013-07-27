<?php
class UserModel extends Model{
	public function authorize($id,$token){
		$res=$this->find($id);
    	if ($res===false || $res==null) {
    		return false;
        }else{
            return ($res['token']==$token);
        }
    }
    public function generateToken($id,$name,$pwd){
    	$condition['id']=$id;
    	$data['token']=md5($id.$name.$pwd);
    	$this->where($condition)->save($data);
        var_dump($data['token']);
    	return $data['token'];
    }
    //各获取三条最新消息
    public function getNews($uid){
        $sql_follow=" SELECT fans.birth,userb,user.uname AS userbname FROM lips_fans AS fans,lips_user AS user WHERE fans.userb=user.id  AND usera=".$uid." ORDER BY fans.birth LIMIT 5 ";
        $sql_likes =" SELECT likes.birth,lid,userb,user.uname AS userbname,line.content AS lcon FROM lips_likes AS likes,lips_user AS user,lips_line AS line WHERE likes.userb=user.id AND likes.lid=line.id AND likes.usera=".$uid." ORDER BY likes.birth LIMIT 5 ";
        $sql_subs  =" SELECT subs.birth,cid,userb,user.uname AS userbname,clip.name AS cname FROM lips_subs AS subs,lips_user AS user,lips_clip AS clip WHERE subs.userb=user.id AND subs.cid=clip.id AND subs.usera=".$uid." ORDER BY subs.birth LIMIT 5 ";
        $result=array();
        $result['fnews']=$this->query($sql_follow);
        $result['lnews']=$this->query($sql_likes);
        $result['snews']=$this->query($sql_subs);
        return $result;
    }
    //获取用户的简要信息
    public function getBriefInfo($uid){
        $sql="SELECT user.id,user.uname,user.udesc,user.gender,user.grade,
             (SELECT count(id) from lips_fans where usera=".$uid.") AS followers,
             (SELECT count(id) from lips_fans where userb=".$uid.") AS following
             FROM lips_user AS user WHERE user.id=".$uid;
        return $this->query($sql);
    }
}
?>