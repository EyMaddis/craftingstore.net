<?php
defined('_MCSHOP') or die("Security block!");

class ShopConfig extends aDisplayable // todo: Bildgröße in die Datenbank reinschreiben
{
	public function prepareDisplay()
	{
		$_SESSION['Index']->assign_say('ADM_CONTENT_SHOP_CONFIG_TITLE');
		$_SESSION['Index']->assign_say('ADM_CONTENT_SHOP_CONFIG_SUBMIT');
		$_SESSION['Index']->assign_say('ADM_CONTENT_SHOP_CONFIG_CANCEL');
		$_SESSION['Index']->assign_say('ADM_CONTENT_STARTING_CREDIT');

		$_SESSION['Index']->assign_say('ADM_CONTENT_HOSTNAME_TITLE');
		$_SESSION['Index']->assign_say('ADM_CONTENT_HOSTNAME_DESCRIPTION');

		$_SESSION['Index']->assign_say('ADM_CONTENT_LOGO_TITLE');
		$_SESSION['Index']->assign('ADM_CONTENT_LOGO_MAX_SIZE', MAX_LOGO_SIZE*1024);
		$_SESSION['Index']->assign_say('ADM_CONTENT_LOGO_INFO', array(MAX_LOGO_SIZE)); // enable html tags, but loaded 
			
		$old_logo =  $_SESSION['Index']->db->fetchOne("SELECT ShopLogo FROM mc_shops WHERE Id = '".$_SESSION['Index']->adminShop->getShopInfo()->Id."' LIMIT 1");


		if(isset($_POST['submit'])){
			if(isNumber($_POST['starting_credit'])){
				$_SESSION['Index']->db->update("UPDATE mc_shops SET StartingCredit='{$_POST['starting_credit']}' WHERE Id='{$_SESSION['Index']->adminShop->getId()}' LIMIT 1");
			}
			else{
				$_SESSION['Index']->assign_say('ADM_SHOPCONFIG_STARTING_CREDIT_ERROR');
			}
			$_SESSION['Index']->assign('ADM_CONTENT_STARTING_CREDIT_VALUE',$_POST['starting_credit']);

#region Hostname
			if($_POST['custom_domain'] != '' && !isValidHostname($_POST['custom_domain'])){
				echo "Invalid hostname";
			}
			else{
				$UsedId = 0;
				if($_POST['custom_domain'] != ''){
					$UsedId = $_SESSION['Index']->db->fetchOne("SELECT Id FROM mc_shops WHERE Domain='{$_POST['custom_domain']}' LIMIT 1");
				}
				if(!$UsedId){
					$_SESSION['Index']->db->update("UPDATE mc_shops SET Domain='{$_POST['custom_domain']}' WHERE Id='".$_SESSION['Index']->adminShop->getShopInfo()->Id."' LIMIT 1");
				}
				elseif($UsedId != $_SESSION['Index']->adminShop->getId()){
					echo "hostname in use";
				}
			}
			$_SESSION['Index']->assign('ADM_CONTENT_HOSTNAME_VALUE',$_POST['custom_domain']);
#end

#region Image
			//This function reads the extension of the file. It is used to determine if the
			// file  is an image by checking the extension.
			/*function getExtension($str){
				$i = strrpos($str,".");
				if(!$i) return "";

				$l = strlen($str) - $i;
				$ext = substr($str,$i+1,$l);
				return $ext;
			}*/
			$errors=0;
			//reads the name of the file the user submitted for uploading

			//if it is not empty
			if($_FILES['image']['name']){
				//get the original name of the file from the clients machine
				$filename = stripslashes($_FILES['image']['name']);

				//get the extension of the file in a lower case format
				$extension = FileUpload::getExtension($filename);
				$extension = strtolower($extension);

				//if it is not a known extension, we will suppose it is an error and 
				// will not  upload the file,  
				//otherwise we will do more tests

				if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif")) {
					//print error message
					$message = 3;
					$errors = 1;
				}
				else{
					//get the size of the image in bytes
					//$_FILES['image']['tmp_name'] is the temporary filename of the file
					//in which the uploaded file was stored on the server
					$size = filesize($_FILES['image']['tmp_name']);

					//compare the size with the maxim size we defined and print error if bigger
					if ($size > MAX_LOGO_SIZE*1024){
						$message = 2;
						$errors = 1;
					}

					//we will give an unique name, for example the time in unix time format
					$image_name=time().'.'.$extension;

					$subdomain = $_SESSION['Index']->adminShop->getShopInfo()->Subdomain;

					$shop_image_dir = $_SERVER['DOCUMENT_ROOT']."/images/shops/".$subdomain."/";

					//the new name will be containing the full path where will be stored -> images/SUBDOMAIN/TIME.EXT

					$newname = $shop_image_dir.$image_name;
					//var_dump ($newname);

					//we verify if the image has been uploaded, and print error instead

					if (!file_exists($shop_image_dir)){
						mkdir($shop_image_dir);
					}
					// save upload to file a on the server
					$copied = move_uploaded_file($_FILES['image']['tmp_name'], $newname);

					if (!$copied) {
						$message = 4;
						$errors=1;
					}
				}
			}
#end
		}
		else{
			$_SESSION['Index']->assign('ADM_CONTENT_STARTING_CREDIT_VALUE',$_SESSION['Index']->adminShop->getShopInfo()->StartingCredit);
			$_SESSION['Index']->assign('ADM_CONTENT_HOSTNAME_VALUE',$_SESSION['Index']->adminShop->getShopInfo()->Domain);
		}

		//var_dump($old_logo);

		// If no errors registered, print the success message, delete the old file and update the database
		if(isset($_POST['submit']) && !$errors){
			if($_FILES['image']['name']){
				if ($old_logo && !unlink($shop_image_dir.$old_logo)){
					$message = 5;
					$errors = 1;
				}
			}
			else{
				$message = 6; 
				$errors = 1;
			}

			if(!$errors){ 
				$_SESSION['Index']->db->query("UPDATE mc_shops SET ShopLogo = '$image_name' WHERE Id = '".$_SESSION['Index']->adminShop->getShopInfo()->Id."' LIMIT 1");
				$_SESSION['Index']->adminShop->updateShopInfo(); // reload cache

				$message = 1;       
			}
		}
		if($message == 1){
			// reload the site
			setLocation ($_SERVER['REQUEST_URI'].'&message='.$message);
		}
		if (isset($_GET['message']) && $_POST['Submit']){
			$message = $_GET['message'];
		}
		$data = null;
		switch ($message)
		{
			case 1:
				$message = "ADM_DESIGN_UPLOAD_SUCCESSFUL";
				break;
			case 2:
				$message = 'You have exceeded the size limit of %dKB';
				$data = array(MAX_LOGO_SIZE);
				break;
			case 3:
				$message = "Unknown file extension, only .gif, .png or .jpg are permitted";
				break;
			case 4:
				$message = 'Saving of image failed, please contact the support!';
				break;
			case 5:
				$message = "Error: permissions to unlink missing";
				break;
			case 6:
				$message = "No file uploaded!";
				break;
			default:
				$message = -1; // the tpl knows what to do
		}
		$_SESSION['Index']->assign_say("ADM_CONTENT_LOGO_MESSAGE", $message, $data);

		$_SESSION['Index']->adminShop->updateShopInfo();
	}
}
?>