<?php
	namespace Models;
	use MS\MSModel;
	use get;
	use PHPMailer;
	class UserModel extends MSModel{
		public $ErrorCode;
		public $ErrorDetail;
		function __construct(){
			parent::__construct();
		}
		function NewUser($UserName = false, $Password = false, $Email = false){
			if($UserName and $Password and $Email){
				if($this->CheckUserByUserName($UserName)){
					$this->ErrorCode = 1;
					$this->ErrorDetail = "This user already exist.";
					return false;
				}
				else if($this->CheckUserByEmail($Email)){
					$this->ErrorCode = 2;
					$this->ErrorDetail = "This email already exist.";
					return false;
				}
				else{
					$ActivationCode = $this->CreateActivationCode();
					$this->insert("high_users","UserName,Password,Email,ActivationCode","'$UserName','$Password','$Email','$ActivationCode'");
					if($this->lastInsertId()>0){
						return true;
					}
					else{
						$this->ErrorCode = 3;
						$this->ErrorDetail = "Failed User Adding.";
						return false;
					}
				}
			}
		}
		function CheckUserByUserName($UserName = false){
			if($UserName){
				$Control = $this->select("high_users","UserName='$UserName'");
				if(count($Control)>0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		function CheckUserByEmail($Email = false){
			if($Email){
				$Control = $this->select("high_users","Email='$Email'");
				if(count($Control)>0){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		function CreateActivationCode($Size = 0){
			$abc = "ABCDEFGHIJKLMNOPRSTUVYZWXQ";
			$numbers = "0123456789";
			$Text = "";
			if($Size == 0){
				$Size = 8; // Default
			}
			for($i=0;$i<=$Size;$i++){
				$Rand1 = rand(0,1);
				if($Rand1 == 0){
					$Text.= $abc[rand(0,strlen($abc))];
				}
				else{
					$Text.= $numbers[rand(0,strlen($numbers))];
				}
			}
			return $Text;
		}
		function RegisterUserInfo($UserId = false,$SubScript = false,$Value = false){
			if($UserId and $SubScript and $Value){
				if($SubScript == "Email" or $SubScript == "Password"){
					$this->update("high_users","$SubScript='$Value'","UserId='$UserId'");
				}
				else{
					if(count($this->select("high_user_info","UserId='$UserId' and SubScript='$SubScript'"))>0){
						$this->update("high_user_info","Value='$Value'","UserId='$UserId' and SubScript='$SubScript'");
					}
					else{
						$this->insert("high_user_info","UserId,SubScript,Value","'$UserId','$SubScript','$Value'");
					}
				}
				return true;
			}
			else{
				return false;
			}
		}
		function UpdateUser($UserId = false, $SubScript = false, $Value = false){
			if($UserId and $SubScript and $Value){
				$this->RegisterUserInfo($UserId,$SubScript,$Value);
			}
		}
		function GetUserInfo($UserId, $SubScript){
			if($UserId and $SubScript){
				if($SubScript == "UserName" or $SubScript == "Email" or $SubScript == "ActivationCode"){
					$Info = $this->select("high_users","UserId='$UserId'","$SubScript");
					if(count($Info)>0){
						return $Info[0][$SubScript];
					}
					else{
						return "";
					}
				}
			}
			return false;
		}
		function IsActive($UyeId = false){
			if($UyeId){
				$Info = $this->select("high_users","UyeId='$UyeId'","IsActivated");
				if(count($Info)>0){
					return $Info[0]["IsActivated"];
				}else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		function IsBanned($UyeId = false){
			if($UyeId){
				$Info = $this->select("high_users","UyeId='$UyeId'","IsBanned");
				if(count($Info)>0){
					return $Info[0]["IsBanned"];
				}else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		function GetActivationCode($UyeId = false){
			if($UyeId){
				$Info = $this->select("high_users","UyeId='$UyeId'","ActivationCode");
				if(count($Info)>0){
					return $Info[0]["ActivationCode"];
				}else{
					return false;
				}
			}
			else{
				return false;
			}
		}
		function PostActivationCode($Title = false, $LeftText = false, $RightText = false){
			//$Mail = new PHPMailer();
			//$Mail->IsSMTP();
		}
		function ActivateUser($UserId = false){
			if($UserId){
				$UserId = $this->Uselib->Clean($UserId);
				if($UserId == ""){
					$this->update("high_users","IsActivated='1'","UserId='$UserId'");
				}
			}
			else{
				return false;
			}
		}
		function DeActivateUser($UserId = false){
			if($UserId){
				$UserId = $this->Uselib->Clean($UserId);
				if($UserId == ""){
					$this->update("high_users","IsActivated='0'","UserId='$UserId'");
				}
			}
			else{
				return false;
			}
		}
		function BanUser($UserId = false){
			if($UserId){
				$UserId = $this->Uselib->Clean($UserId);
				if($UserId == ""){
					$this->update("high_users","IsBanned='1'","UserId='$UserId'");
				}
			}
			else{
				return false;
			}
		}
		function UnBanUser($UserId = false){
			if($UserId){
				$UserId = $this->Uselib->Clean($UserId);
				if($UserId == ""){
					$this->update("high_users","IsBanned='0'","UserId='$UserId'");
				}
			}
			else{
				return false;
			}
		}
	}
?>