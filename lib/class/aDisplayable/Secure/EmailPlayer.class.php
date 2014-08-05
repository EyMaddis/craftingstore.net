<?php
defined('_MCSHOP') or die("Security block!");

class EmailPlayer extends aDisplayable
{
	public function prepareDisplay()
	{
            $noreply = 'noreply@'.BASE_DOMAIN;
            $shopownername = 'Mathis Neumann';
            $shopownermail = 'mathis@'.BASE_DOMAIN;
            
            $error = "";
            if(isset($_GET['playerId']) && is_numeric($_GET['playerId'])){
                $player = $_SESSION['Index']->user->getUserDataById($_GET['playerId']);
                $playername = $player->Nickname;
            }
            else {
                header("Location: ./?show=RegisteredPlayers");
            }
            if (isset($_POST['submit'])){
                if (isset($_POST['message']) && isset($_POST['subject'])) {
                    if(mail($player->Email, $_POST['subject'], $_POST['message']."\r\n \r\n Please reply to $shopownermail, because this is an automated mail adress which never check! \r\n -- Your Minecraftshop.net Team", "From: $shopownername <$shopownermail>\r\nReply-To: $shopownermail")) {
                        header("Location: ./?show=RegisteredPlayers&mailsent=1") or die("MAIL NOT WORKING!");
                    } else {
                        $error = "ERROR: Couldn't send mail";
                        die($error);
                    }
                }
            } 
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_INFO", "If you send a mail to a player, the player is able to see your email adress and can respond directly to your mail!");
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_ERROR",$error);
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_TITLE", "Send a mail to");
            $_SESSION['Index']->assign("ADM_EMAIL_PLAYER_PLAYERNAME", $playername);
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_SUBJECT", "Subject:");
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_MESSAGE", "Message:");
            $_SESSION['Index']->assign_say("ADM_EMAIL_PLAYER_SEND", "submit");
            

	}
}
?>