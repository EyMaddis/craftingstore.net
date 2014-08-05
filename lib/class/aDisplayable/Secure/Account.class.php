<?php
defined('_MCSHOP') or die("Security block!");

class Account extends aDisplayable
{
	function prepareDisplay()
	{
		$comma = $_SESSION['Index']->say('COMMA');
		$_SESSION['Index']->assign_say('ADM_ACCOUNTS_TITLE');
		$_SESSION['Index']->assign_say('ADM_ACCOUNTS_PAYOUTS_TITLE');


		#region Umsatz-Übersicht
		$_SESSION['Index']->assign_say('ADM_ACCOUNTS_OVERVIEW_TITLE');
		$_SESSION['Index']->assign_say('ADM_ACCOUNT_REVENUE_CURRENT_SHOP_LABEL');
		$_SESSION['Index']->assign_say('ADM_ACCOUNT_REVENUE_ALL_SHOP_LABEL');
		$_SESSION['Index']->assign_say('ADM_ACCOUNT_ACCOUNT_CURRENT_LABEL');

		$AccountCurrent = $_SESSION['Index']->db->fetchOne("SELECT Current FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' ORDER BY Time DESC LIMIT 1");
		$_SESSION['Index']->assign_direct('ADM_ACCOUNT_ACCOUNT_CURRENT',str_replace('.', $comma, sprintf('%01.2f',$AccountCurrent/100)));
		$_SESSION['Index']->assign_direct('ADM_ACCOUNT_REVENUE_CURRENT_SHOP',str_replace('.',$comma,sprintf('%01.2f',$_SESSION['Index']->db->fetchOne("SELECT Sum(Difference) FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' AND ShopId='{$_SESSION['Index']->adminShop->getId()}' AND PayoutStatus IS NULL")/100)));
		$_SESSION['Index']->assign_direct('ADM_ACCOUNT_REVENUE_ALL_SHOP',str_replace('.',$comma,sprintf('%01.2f', $_SESSION['Index']->db->fetchOne("SELECT Sum(Difference) FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' AND PayoutStatus IS NULL") / 100)));
		#endregion

		#region Kann eine Auszahlung beantragt werden?
		$PaypalMail = $_SESSION['Index']->db->fetchOne("SELECT PaypalMail FROM mc_customers WHERE Id='{$_SESSION['CustomerId']}' LIMIT 1");
		if(!$PaypalMail)
		{
			$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT_NO_MAIL');
			$_SESSION['Index']->assign_say('ADM_ACCOUNT_TO_PROFILE');
		}
		else
		{
			$lastPayout = $_SESSION['Index']->db->fetchOne("SELECT Time FROM mc_customeraccounts WHERE CustomersId='{$_SESSION['CustomerId']}' AND PayoutStatus IS NOT NULL ORDER BY Time DESC LIMIT 1");
			if(($lastPayout + 86400*30 < time()) && ($AccountCurrent >= 1000))
			{
				$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT');
			}
			else
			{
				$_SESSION['Index']->assign_say('ADM_ACCOUNT_REQUEST_PAYOUT_FORBIDDEN');
			}
		}
		#endregion

		#region Auszahlungen
		$transaction_pending_label = $_SESSION['Index']->say('ADM_ACCOUNT_PAYOUT_PENDING');
		$transaction_finished_label = $_SESSION['Index']->say('ADM_ACCOUNT_PAYOUT_FINISHED');
		$payouts = array();
		foreach($_SESSION['Index']->db->iterate("
SELECT c1.Difference, c1.Time AS ValutaTime, c1.PayoutStatus, c2.Time AS BookingTime FROM mc_customeraccounts AS c1
LEFT JOIN mc_customeraccounts AS c2
ON c2.PayoutStatus=c1.Id
WHERE c1.CustomersId='{$_SESSION['CustomerId']}' AND c1.PayoutStatus=0 ORDER BY c1.Time DESC") as $row)
		{
			if($row->BookingTime)
			{
				$bookingTime = date('d.m.Y H:i:s', $row->BookingTime);
				$Icon = 'transaction_finished.png';
				$info = $transaction_finished_label;
			}
			else
			{
				$bookingTime = '';
				$Icon = 'transaction_pending.png';
				$info = $transaction_pending_label;
			}
				
			$payouts[] = array
				(
					'Transaction' => $bookingTime,
					'Valuta' => date('d.m.Y H:i:s', $row->ValutaTime),
					'Difference' => str_replace('.',$comma,sprintf('%01.2f',$row->Difference/100)),
					'Info' => $info,
					'Icon' => $Icon
				);
		}
		if(count($payouts))
			$_SESSION['Index']->assign_direct('ADM_ACCOUNTS_PAYOUTS', $payouts);
		else
			$_SESSION['Index']->assign_say('ADM_ACCOUNTS_PAYOUTS_NONE');
		#endregion
	}
}

?>