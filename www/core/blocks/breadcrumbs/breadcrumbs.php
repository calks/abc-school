<?php

class BreadcrumbsBlock extends Block 
{
	public function run($params = array()) 
	{
		if( Router :: getSourceUrl() == '' )
		{
			return $this->terminate();	
		}

		if( Request :: get( 'content_only' ) ) 
		{
			return;	
		}

		$smarty = Application :: getSmarty();
		$breadcrumbs = Application :: getBreadcrumbs();
		
		$template_path = $this->getTemplatePath();
		$smarty->assign( 'path', $breadcrumbs->getPath() );

		return $smarty->fetch( $template_path );
	}
}
