<?php
defined('_MCSHOP') or die("Security block!");

class Pagination
{
	private $itemsPerPage = 15;
	private $page = 1;
	private $maxPages = 4;#maximale Anzahl der Seitenbuttons, die vor bzw. hinter der aktuellen Seite angezeigt werden

	public function prepare($count, $pageUrl, &$first, &$number)
	{
		$_SESSION['Index']->assign_say('ADM_LATEST_TITLE');
		$_SESSION['Index']->assign('ADM_PAGE_URL', $pageUrl.'&');

		if(isNumber($_GET['page']))
		{
			$this->page = $_GET['page'];
		}
		$lastPage = ceil($count/$this->itemsPerPage);
		if($lastPage > 1)
		{
			if($this->page > $lastPage) $this->page = 1;
			$_SESSION['Index']->assign_direct('ADM_PAGE_SHOW_NUMBERS', true);
			$_SESSION['Index']->assign_direct('ADM_PAGE', $this->page);
			$_SESSION['Index']->assign_direct('ADM_PAGE_LAST', $lastPage);
			$_SESSION['Index']->assign_direct('ADM_PAGE_BEFORE', ($this->page > 1 ? $this->page - 1 : null));
			$_SESSION['Index']->assign_direct('ADM_PAGE_NEXT', ($this->page < $lastPage ? $this->page + 1 : null));

			$startPage = $this->page - $this->maxPages;
			if($startPage < 1) $startPage = 1;
			$endPage = $this->page + $this->maxPages;
			if($endPage > $lastPage) $endPage = $lastPage;
			$_SESSION['Index']->assign_direct('ADM_PAGE_START', $startPage);
			$_SESSION['Index']->assign_direct('ADM_PAGE_END', $endPage);
		}
		else
			$this->page = 1;

		$first = ($this->page - 1) * $this->itemsPerPage;
		$number = $this->itemsPerPage;
	}
}
?>