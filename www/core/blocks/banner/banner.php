<?php

Application :: loadObjectClass( 'banners' );

class BannerBlock extends Block
{
	public function run( $params = array() )
	{
		$positionH = isset( $params[ 'positionH' ] )
			? $params[ 'positionH' ]
			: '';
		
		$positionV = isset( $params[ 'positionV' ] )
			? $params[ 'positionV' ]
			: '';
					
		$url = Router :: getRewrittenUrl();
		
		// Kill url leading slash if exists
		if( '/' == substr( $url, 0, 1 ) )
		{
			$url = explode( '/', $url );
			array_shift( $url );
			$url = implode( '/', $url );
		}
		
		if( $banner = banners :: search( $positionH, $positionV, $url ) )
		{
			$smarty = application :: getSmarty();
			$smarty->assign( 'banner', $banner );
			
			$template_path = $this->getTemplatePath( 'banner' );
			return $smarty->fetch( $template_path );
		}
		else return '';
	}
}