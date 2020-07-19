<?php
// Class that provides methods for working with the form data.
// There should be NOTHING in this file except this class definition.

class SimpleController {
	private $product;
	private $user;

	public function __construct() {
		global $f3;		
		$this->f3 = $f3;				
		$this->user = new DB\SQL\Mapper($f3->get('DB'),"Magic2_user");
		$this->product = new DB\SQL\Mapper($f3->get('DB'),"Magic2_product");	
		$this->db=new DB\SQL(
    	'mysql:host=localhost;port=3306;dbname=s1928233',
    	's1928233',
    	'K5qApXPgDw');
    }

	public function loginUser($user, $pwd) {
		$auth = new \Auth($this->user, array('id'=>'username', 'pw'=>'password'));
		return $auth->login($user, $pwd);
	}

	public function signupUser($data) {	
		$this->user->username = $data["name"];					
		$this->user->password = $data["pass"];
		$this->user->save();									
	}
	
	public function getSearchResult($data) {
		
		$sql = "SELECT productid,sellerid,abstract,title FROM s1928233.Magic2_product where title regexp'(".$data.")' union SELECT productid,sellerid,abstract,title FROM s1928233.Magic2_product where abstract regexp'(".$data.")'";

		$result = $this->db->exec($sql);

		for ($i=0; $i<count($result); $i++) {
			$sql = "SELECT username FROM Magic2_user where userid=".$result[$i]['sellerid'];
			$username = $this->db->exec($sql);
			$result[$i]['sellerid']=$username[0]['username'];
		} 

		$this->f3->set('numberOfResult',count($result));
		$this->f3->set('div',$result);
	}

	public function getCommunity() {

		 $sql = "SELECT distinct(community) FROM Magic2_product";

		 $result = $this->db->exec($sql);

		 $this->f3->set('div',$result);
	}

	public function getCommunityList($data){
		$sql = "SELECT productid,sellerid,abstract,title FROM Magic2_product where community='".$data."'";

		$result = $this->db->exec($sql);

		for ($i=0; $i<count($result); $i++) {
			$sql = "SELECT username FROM Magic2_user where userid=".$result[$i]['sellerid'];
			$username = $this->db->exec($sql);
			$result[$i]['sellerid']=$username[0]['username'];
		} 

		$this->f3->set('div',$result);
	}

	public function getContent($id){
		$sql ="SELECT email,phone,community,story,abstract,title FROM Magic2_product where productid='".$id."'";
		$result = $this->db->exec($sql);
		$this->f3->mset(
			array(
				'pid'=>$id,
				'pemail'=>$result[0]['email'],
				'pphone'=>$result[0]['phone'],
				'pcommunity'=>$result[0]['community'],
				'pstory'=>$result[0]['story'],
				'pabstract'=>$result[0]['abstract'],
				'ptitle'=>$result[0]['title']
			)
		);
	}

	public function getPublication($data){
		$sql ="SELECT email,phone,sellerid,story,abstract,title FROM Magic2_product where productid='".$data."'";
		$result = $this->db->exec($sql);

		$sql = "SELECT username FROM Magic2_user where userid=".$result[0]['sellerid'];
		$username = $this->db->exec($sql);

		$this->f3->set('pub_title',$result[0]['title']);
		$this->f3->set('pub_abstract',$result[0]['abstract']);
		$this->f3->set('pub_story',$result[0]['story']);
		$this->f3->set('pub_user',$username[0]['username']);
		$this->f3->set('email',$result[0]['email']);
		$this->f3->set('phone',$result[0]['phone']);
		$this->f3->set('pub_id',$data);
		
		if(strpos($result[0]['title'],"[SOLD OUT]")!==false){
			$this->f3->set('sold','true');
		}else{
			$this->f3->set('sold','false');
		}
	}

	// public function getF3(){
	// 	return $this->f3;
	// }
	public function setOrder($wantPro, $username, $myPro){
		//get my user id
		$sql = "SELECT userid FROM Magic2_user WHERE username='".$username."'";
		$result = $this->db->exec($sql); 
		$userid = $result[0]['userid'];
		
		//true my pro - myPro's sellerid is userid
		$sql = "SELECT sellerid FROM Magic2_product WHERE productid='".$myPro."'";
		$result = $this->db->exec($sql); 
		if($result[0]['sellerid']===$userid){
			//get want pro followers
			$sql = "SELECT sellerid,followers,title FROM Magic2_product WHERE productid='".$wantPro."'";
			$result = $this->db->exec($sql); 
			$wantProFol = $result[0]['followers'];
			$sellerid = $result[0]['sellerid'];
			$title1 = $result[0]['title'];

			//add my pro into want pro followers
			$wantProFol = $wantProFol.",".$myPro; //,userid
			$sql = "UPDATE Magic2_product SET followers='".$wantProFol."'where productid='".$wantPro."'";
			$this->db->exec($sql); 
			//check both exist - get my pro fol
			$sql = "SELECT title,followers FROM Magic2_product WHERE (productid='".$myPro."' and sellerid='".$userid."')";
			$result = $this->db->exec($sql); 
			$myProFol = $result[0]['followers'];
			$title2 = $result[0]['title'];
			if(strpos($title1,"[SOLT OUT]")!==false || strpos($title2,"[SOLT OUT]")!==false){
				//do nothing
			}else if(strpos($myProFol,",".$wantPro)!==false){
				//set customerid
				$title1 = "[SOLD OUT] ".$title1;
				$title2 = "[SOLD OUT] ".$title2;
				
				$title1 = str_replace("'","\'",$title1);
    			$title1 = str_replace('"','\"',$title1);
    			$title2 = str_replace("'","\'",$title2);
    			$title2 = str_replace('"','\"',$title2);
				// can make robust
				$sql = "UPDATE Magic2_product SET customerid='".$sellerid."', title='".$title2."' WHERE productid='".$myPro."'";
				$this->db->exec($sql); 
				$sql = "UPDATE Magic2_product SET customerid='".$userid."', title='".$title1."' WHERE productid='".$wantPro."'";
				$this->db->exec($sql); 
			}
		}
		


	}

	public function getMyOrder($data){
		$sql ="SELECT productid,sellerid,abstract,title FROM Magic2_product where customerid=(select Magic2_user.userid from Magic2_user where Magic2_user.username='".$data."')";
		$result = $this->db->exec($sql);

		for ($i=0; $i<count($result); $i++) {
			$sql = "SELECT username FROM Magic2_user where userid=".$result[$i]['sellerid'];
			$username = $this->db->exec($sql);
			$result[$i]['sellerid']=$username[0]['username'];
		} 

		$this->f3->set('numberOfMyOrd',count($result));
		$this->f3->set('div',$result);


	}

	public function getUserPublish($data){
		$sql ="SELECT productid,sellerid,abstract,title FROM Magic2_product where sellerid=(select Magic2_user.userid from Magic2_user where Magic2_user.username='".$data."')";
		$result = $this->db->exec($sql);

		$this->f3->set('numberOfMyPub',count($result));
		$this->f3->set('div',$result);
	}

	public function resetPassword($user,$pass,$passCon){
		if($pass===$passCon){
			$sql = "UPDATE Magic2_user SET password='".$pass."'where Magic2_user.username='".$user."'";
			$this->db->exec($sql);
			return true;
		}else{
			return false;
		}
	}

	public function uniqueName($data) {
		$name = $data["name"];
		$sql = "SELECT username FROM Magic2_user WHERE username=?";
		$result = $this->db->exec($sql,$name); 
		if (count($result)===0){
			return true;
		} 
		return false;
	}

	public function deletePublish($idToDelete) {
		$this->product->load(['productid=?', $idToDelete]);				// load DB record matching the given ID
		$this->product->erase();									// delete the DB record
	}
	
	public function replacechar($string){
    	$string = str_replace("'","\'",$string);
    	return str_replace('"','\"',$string);
    }

	public function setPublish($data){
		$title = $data["title"];
		$abstract = $data["abstract"];
		$story = $data["story"];
		$seller = $data["seller"];
		$email = $data["email"];
		$phone = $data["phone"];
		$id = $data["id"];

		if ($data["community"]!==""){
			$community = $data["community"];
		}
		if($data["community1"]!==""){
			$community = $data["community1"];
		}

		$title = str_replace("'","\'",$title);
    	$title = str_replace('"','\"',$title);
		$abstract = str_replace("'","\'",$abstract);
    	$abstract = str_replace('"','\"',$abstract);
		$story = str_replace("'","\'",$story);
    	$story = str_replace('"','\"',$story);

		if($id !== "" ){
			$sql = "UPDATE Magic2_product SET abstract='".$abstract."', community='".$community."', title='".$title."', story='".$story."', email='".$email."', phone='".$phone."' WHERE productid='".$id."'";
			$this->db->exec($sql);
		}else{
			$sql = "SELECT userid FROM Magic2_user where username = '".$seller."'";
			$result = $this->db->exec($sql);
			$sellerid = $result[0]['userid'];

			$sql = "INSERT INTO Magic2_product (sellerid, story, community, abstract, title,email,phone) VALUES ('".$sellerid."', '".$story."','".$community."','".$abstract."','".$title."','".$email."','".$phone."')";
			$this->db->exec($sql);

			//get id
			$sql = "SELECT productid FROM Magic2_product where title = '".$title."'";
			$result = $this->db->exec($sql);
			$id = $result[0]['productid'];
			
		}
		return $id;

		//echo ;
	}
	// public function getProduct($text) {
	// 	$list = $this->mapper->->find();				// load DB record matching the given ID
	// 	return $list;						// delete the DB record
	// }
}

?>
