{if !$AdminStructurePrepared}{$main->useAjax(false)}{$main->prepare('Structure')}<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>{include file="header.tpl"}</head>
<body>{include file="Feedback.tpl"}
{else}
</body>
</html>{/if}