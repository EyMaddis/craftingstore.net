<?php
defined('_MCSHOP') or die("Security block!");

class RequestPayout extends aDisplayable
{
	function prepareDisplay()
	{
		$comma = $_SESSION['Index']->say('COMMA');
		$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_TITLE');

		#region Kann eine Auszahlung beantragt werden?
		$PaypalMail = $_SESSION['Index']->db->fetchOne("SELECT PaypalMail FROM mc_customers WHERE Id='{$_SESSION['CustomerId']}' LIMIT 1");
		if($PaypalMail)
		{
			$AccountCurrent = $_SESSION['Index']->db->fetchOne("SELECT Current FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' ORDER BY Time DESC LIMIT 1");
			$lastPayout = $_SESSION['Index']->db->fetchOne("SELECT Time FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' AND PayoutStatus IS NOT NULL ORDER BY Time DESC LIMIT 1");
			if(($lastPayout + 86400*30 < time()) && ($AccountCurrent >= 1000))
			{
				#region Es wurde ein Betrag übergeben
				if(isset($_POST['amount']))
				{
					$amount = $_POST['amount'];
					#region Das Format ist gültig
					if(preg_match('/^([0-9]+[,\.]?[0-9]{0,2})$/', $amount))
					{
						$amount = str_replace(',','.',$amount)*100; #Komma in Punkt und € in Cent umwandeln
						#region Der Betrag ist gültig
						if($AccountCurrent >= $amount)
						{
							$newAmount = $AccountCurrent-$amount;
							$_SESSION['Index']->db->query("INSERT INTO mc_customeraccounts (CustomersId,Current,Difference,PayoutStatus,PayoutMail,Time) VALUES ('{$_SESSION['CustomerId']}','$newAmount','$amount','0','".mysql_real_escape_string($PaypalMail)."','".time()."')");
							$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_DONE');
							$_SESSION['Index']->assign_say('BACK');
							$beantragt = true;
						}
						#endregion
						#region Der Betrag ist ungültig
						else
						{
							$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_ERROR','ADM_REQUEST_PAYOUT_ERROR_AMOUNT');
							$_SESSION['Index']->assign('DEFAULT_VALUE', $_POST['amount']);
						}
						#endregion
					}
					#endregion
					#region Das Format ist ungültig
					else
					{
						$_SESSION['Index']->assign('DEFAULT_VALUE', $amount);
						$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_ERROR','ADM_REQUEST_PAYOUT_ERROR_FORMAT');
					}
					#endregion
				}
				else
				{
					$_SESSION['Index']->assign('DEFAULT_VALUE', str_replace('.',$comma,sprintf('%01.2f', $AccountCurrent/100)));
				}
				#endregion
				#region Texte für den Input ausgeben
				if(!$beantragt)
				{
					$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT');
					$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_HOW_MUCH');
					$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_AMOUNT');
					$_SESSION['Index']->assign_say('ADM_REQUEST_PAYOUT_REQUEST');
				}
				#endregion
			}
			#region Es darf zur Zeit keine Auszahlung beantragt werden
			else
			{
				$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT_FORBIDDEN');
				$_SESSION['Index']->assign_say('BACK');
			}
			#endregion
		}
		#endregion
		#region Es darf zur Zeit keine Auszahlung beantragt werden
		else
		{
			$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL');
			$_SESSION['Index']->assign_say('ADM_ACCOUNT_TO_PROFILE');
		}
		#endregion
	}
}

?>