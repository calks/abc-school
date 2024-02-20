<?php

class StringUtils {
  /**
   * Checks if the value is valid e-mail address
   * @param string $value
   * @return int
   */
  function isValidEmail($value) {
    return preg_match("/^([\w-~_]+\.)*[\w-~_]+@([\w-_]+\.){1,3}\w{2,4}\$/", $value);
  }

  /**
   * Format the argument as DDD-DDD-DDD-...-DDD-DDDD
   * Simple version, only puts hyphens in the number
   * TODO: Add separator and parenthes processing
   * @param string $phone
   * @return string
   */
  function formatPhone($phone){
    $phone = preg_replace("/[^0-9\+]+/", "", $phone);

    $res = "";
    $shift = 4;
    $second_group = false;

    $len = strlen($phone);
    while ($len){
      if ($second_group) $res = "-" . $res;
      $res = substr($phone, -$shift) . $res;
      $phone = substr($phone, 0, -$shift);
      $shift = 3;
      $second_group = true;
      $len = strlen($phone);
    }
    return $res;
  }

  function truncateString($string, $length, $added_text='...') {
    $string = strip_tags($string);
    if (strlen($string) <= $length) return $string;
    $words = explode(' ', $string);
    $out = '';
    while(strlen($out) < $length) $out .= ' ' . array_shift($words);
    return ltrim($out) . $added_text;
  }
  
	public static function urlDocument( $url )
	{
		Application :: loadLibrary( 'seo/rewrite' );
		if( preg_match( "/^https?:\/\//i", $url ) )
			$result = $url;
		else
		{
			if( substr( $url, 0, 1 ) != '/' )
				$url = '/' . $url;
			$result = Application :: getSeoUrl( $url );
		}
		return $result;
	}
}
