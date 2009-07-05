<?php
/*
 *  display.git_search_files.php
 *  gitphp: A PHP git repository browser
 *  Component: Display - search in files
 *
 *  Copyright (C) 2009 Christopher Han <xiphux@gmail.com>
 */

require_once('defs.constants.php');
require_once('util.highlight.php');
require_once('gitutil.git_filesearch.php');
require_once('gitutil.git_read_commit.php');

function git_search_files($projectroot, $project, $hash, $search, $page = 0)
{
	global $tpl,$gitphp_conf;

	$tpl->clear_all_assign();
	if (!($gitphp_conf['search'] && $gitphp_conf['filesearch'])) {
		$tpl->assign("message","File search has been disabled");
		$tpl->display("message.tpl");
		return;
	}

	if (!isset($search) || (strlen($search) < 2)) {
		$tpl->assign("error",TRUE);
		$tpl->assign("message","You must enter search text of at least 2 characters");
		$tpl->display("message.tpl");
		return;
	}
	if (!isset($hash)) {
		//$hash = git_read_head($projectroot . $project);
		$hash = "HEAD";
	}

	$co = git_read_commit($projectroot . $project, $hash);

	$filesearch = git_filesearch($projectroot . $project, $hash, $search, false, ($page * 100), 101);

	if (count($filesearch) < 1) {
		$tpl->assign("message","No matches for '" . $search . "'.");
		$tpl->display("message.tpl");
		return;
	}

	$tpl->assign("project",$project);
	$tpl->assign("hash",$hash);
	$tpl->assign("treehash",$co['tree']);

	$tpl->assign("search",$search);
	$tpl->assign("searchtype","file");
	$tpl->assign("page",$page);
	$filesearchcount = count($filesearch);
	$tpl->assign("filesearchcount",$filesearchcount);

	$tpl->assign("title",$co['title']);

	$filesearchlines = array();
	$i = 0;
	foreach ($filesearch as $file => $data) {
		$filesearchline = array();
		$filesearchline["file"] = $file;
		if (strpos($file,"/") !== false) {
			$f = basename($file);
			$d = dirname($file);
			if ($d == "/")
				$d = "";
			$hlt = highlight($f, $search, "searchmatch");
			if ($hlt)
				$hlt = $d . "/" . $hlt;
		} else
			$hlt = highlight($file, $search, "searchmatch");
		if ($hlt)
			$filesearchline["filename"] = $hlt;
		else
			$filesearchline["filename"] = $file;
		$filesearchline["hash"] = $data['hash'];
		if ($data['type'] == "tree")
			$filesearchline["tree"] = TRUE;
		if (isset($data['lines'])) {
			$matches = array();
			foreach ($data['lines'] as $line) {
				$hlt = highlight($line,$search,"searchmatch",floor(GITPHP_TRIM_LENGTH*1.5),true);
				if ($hlt)
					$matches[] = $hlt;
			}
			if (count($matches) > 0)
				$filesearchline["matches"] = $matches;
		}
		$filesearchlines[] = $filesearchline;
		$i++;
		if ($i >= 100)
			break;
	}
	$tpl->assign("filesearchlines",$filesearchlines);
	$tpl->display("searchfiles.tpl");
}

?>