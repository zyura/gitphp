<?php
/*
 *  display.git_project_index.php
 *  gitphp: A PHP git repository browser
 *  Component: Display - project index
 *
 *  Copyright (C) 2008 Christopher Han <xiphux@gmail.com>
 */

 require_once('gitutil.git_read_projects.php');

function git_project_index()
{
	global $tpl;

	header("Content-type: text/plain; charset=utf-8");
	header("Content-Disposition: inline; filename=\"index.aux\"");

	$projectlist = GitPHP_ProjectList::GetInstance()->GetConfig();

	$cachekey = sha1(serialize($projectlist));

	if (!$tpl->is_cached('projectindex.tpl', $cachekey)) {
		if (is_array($projectlist))
			$tpl->assign("categorized", TRUE);
		$projlist = git_read_projects();
		$tpl->assign("projlist", $projlist);
	}
	$tpl->display('projectindex.tpl', $cachekey);
}

?>
