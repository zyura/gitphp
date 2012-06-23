<?php
/**
 * Controller for displaying a project summary
 *
 * @author Christopher Han <xiphux@gmail.com>
 * @copyright Copyright (c) 2010 Christopher Han
 * @package GitPHP
 * @subpackage Controller
 */
class GitPHP_Controller_Project extends GitPHP_ControllerBase
{

	/**
	 * Gets the template for this controller
	 *
	 * @return string template filename
	 */
	protected function GetTemplate()
	{
		return 'project.tpl';
	}

	/**
	 * Gets the cache key for this controller
	 *
	 * @return string cache key
	 */
	protected function GetCacheKey()
	{
		return '';
	}

	/**
	 * Gets the name of this controller's action
	 *
	 * @param boolean $local true if caller wants the localized action name
	 * @return string action name
	 */
	public function GetName($local = false)
	{
		if ($local) {
			return __('summary');
		}
		return 'summary';
	}

	/**
	 * Read query into parameters
	 */
	protected function ReadQuery()
	{
	}

	/**
	 * Loads data for this template
	 */
	protected function LoadData()
	{
		$head = $this->GetProject()->GetHeadCommit();
		$this->tpl->assign('head', $head);
		if (!$head)
			$this->tpl->assign('enablesearch', false);

		$revlist = new GitPHP_Log($this->GetProject(), $this->GetProject()->GetHeadCommit(), 17);
		$revlist->SetCompat($this->GetProject()->GetCompat());
		if ($this->config->HasKey('largeskip')) {
			$revlist->SetSkipFallback($this->config->GetValue('largeskip'));
		}

		if ($revlist->GetCount() > 16) {
			$this->tpl->assign('hasmorerevs', true);
			$revlist->SetLimit(16);
		}
		$this->tpl->assign('revlist', $revlist);

		$taglist = $this->GetProject()->GetTagList()->GetOrderedTags('-creatordate', 17);
		if ($taglist) {
			if (count($taglist) > 16) {
				$this->tpl->assign('hasmoretags', true);
				$taglist = array_slice($taglist, 0, 16);
			}
			$this->tpl->assign('taglist', $taglist);
		}

		$headlist = $this->GetProject()->GetHeadList()->GetOrderedHeads('-committerdate', 17);
		if ($headlist) {
			if (count($headlist) > 17) {
				$this->tpl->assign('hasmoreheads', true);
				$headlist = array_slice($headlist, 0, 16);
			}
			$this->tpl->assign('headlist', $headlist);
		}
	}

}
