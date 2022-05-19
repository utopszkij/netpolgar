<?php 

namespace App\Models;

class Minimarkdown {
/**
* minimarkdown to html konverter
* értelmezve: 
*      http......     link
*      ![](http...)   kép url
*      **...**        kiemelt szöveg
*      *....*  dölt betüs szöveg
*      :(  :)  :|     emotions 
*      {log}...EOF változás log
*/

	/**
	* távoli file infok lekérdezése teljes letöltés nélkül
	* csak 'http' -vel kezdödő linkeket ellenöriz
	* @param string $url
	* @return array ['fileExist', 'fileSize' ]
	*/
	public static function getRemoteFileInfo($url) {
			if ((substr($url,0,4) == 'http') & (strpos($url,':') == 0)) {
			   $ch = curl_init($url);
			   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			   curl_setopt($ch, CURLOPT_HEADER, TRUE);
			   curl_setopt($ch, CURLOPT_NOBODY, TRUE);
			   $data = curl_exec($ch);
			   $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			   if ($httpResponseCode == 200) {
			       $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
			       $result = [
			           'fileExists' => (int) $httpResponseCode == 200,
			           'fileSize' => (int) $fileSize
			       ];
			       curl_close($ch);
			   } else {
			       $result = [
			           'fileExists' =>  0,
			           'fileSize' => (int) $fileSize
			       ];
			       curl_close($ch);
			   }
			} else {
			   $result = [
		        'fileExists' => 1,
		        'fileSize' => 100
			   ];
			}
			return $result;
	}
	
	public static function str_replace_first($search, $replace, $subject)
	{
	    $search = '/'.preg_quote($search, '/').'/';
	    return preg_replace($search, $replace, $subject, 1);
	}
	
	/**
	 * mini markdown parser
	 * @param markdown string $s
	 * @return html string
	 */
	public static function miniMarkdown($s) {
		$s = strip_tags($s,['br<img src="homokozo.png" alt="" /><img src="homokozo.png" alt="" />']);
	    $s = str_replace("\r\n",'<br />',$s);       // \r\n --> <br />
	    $s = str_replace("\n",'<br />',$s);         // \n --> <br />
	    $s = str_replace("\r",'<br />',$s);         // \r --> <br />
	    $w = explode('{log}',$s);
		$log = '';
		$s = $w[0];
		if (count($w) > 1) {
			$log = $w[1];
		}
		$imgs = [];
		 preg_match_all('~!\[\]\([^\s<]+\)~',$s, $imgs);
		 // ne legyen több mint 3 kép
		 for ($i=3; $i<count($imgs[0]); $i++) {
		 		$s = Minimarkdown::str_replace_first($imgs[0][$i],'',$s);
		 }		 
	    $imgs = [];
		 preg_match_all('~!\[\]\([^\s<]+\)~',$s, $imgs);
		 // túl bagy képek törlése	
		 foreach ($imgs[0] as $img) {
			 	$imgUrl = str_replace('![](','',$img);
			 	$imgUrl = str_replace(')','',$imgUrl);
			 	$fileInfo = Minimarkdown::getRemoteFileInfo($imgUrl);
			 	if ($fileInfo['fileSize'] > 2000000) {
			 		// túl nagy kép, törlöm az összes előfordulását
			 		$s = str_replace($img,'',$s);
			 	}	
	 	 }
	    $s = str_replace('![](http', 'img(', $s);   // kép url -t ne konvertálja <a -ra
	
	    // link to <a href=...">...</a>
	    //$url = '~(?:(https?)://([^\s<]+)|(wwww\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
	    $url = '~(?:(https?)://([^\s<]+))(?<![\.,:])~i';
	    $s = preg_replace($url, '<a href="$0">$0</a>', $s);
	
	    // img(....) -> img src="..." />
	    $img = '~img\(([^\s<]+)\)~i';
	    $s = preg_replace($img, '<img src="$0)" />', $s);
	    $s = str_replace('img(','http',$s);
	    $s = str_replace('))','',$s);
	
	
	    // **...** -> <strong>...</strong>
	    $bold = '~\*\*([^\*<]+)\*\*~i';
	    $s = preg_replace($bold, '<strong>$0</strong>', $s);
	    $s = str_replace('**','',$s);
	    // *...* -> <em>...</em>
	    $em = '~\*([^\*<]+)\*~i';
	    $s = preg_replace($em, '<em>$0</em>', $s);
	    $s = str_replace('<em>*','<em>',$s);
	    $s = str_replace('*</em>','</em>',$s);

	    // ##.....<br /> -> <h2>.....</h2>
		$h2 = '~\#\#([^\<<]+)\<~i';
	    $s = preg_replace($h2, '<h2>$0/h2>', $s);
	    $s = str_replace('<h2>##','<h2>',$s);
	    $s = str_replace('</h2>br />','</h2>',$s);

	    // #.....<br /> -> <h1>.....</h1>
		$h1 = '~\#([^\<<]+)\<~i';
	    $s = preg_replace($h1, '<h1>$0/h1>', $s);
	    $s = str_replace('<h1>#','<h1>',$s);
	    $s = str_replace('</h1>br />','</h1>',$s);

		 // emotions
	    $s = str_replace(':)','<em class="fas fa-grin"></em>',$s);
	    $s = str_replace(':(','<em class="fas fa-frown"></em>',$s);
	    $s = str_replace(':|','<em class="fas fa-flushed"></em>',$s);
		if ($log != '') {
			$s .= '<p class="logLink" onclick="'."$('.log').toggle()".'">Módosítva</p>
			<div class="log" style="display:none">'.$log.'</div>';
		}
	    return $s;
	}

	/**
	 * log eltávolitása a minimarkdown stringből
	 * @param string miniMarkdownBody + log
	 * @return string miniMarkdownBody
	 */
	public static function stripLog(string $s): string {
		$log = '';
		$w = explode('{log}',$s);
		$s = $w[0];
		if (count($w) > 1) {
			$log = $w[1];
		}
		return $s;
	}

	/**
	 * log kiemelése a minimarkdown stringből
	 * @param string miniMarkdownBody + log
	 * @return string log
	 */
	public static function getLog(string $s): string {
		$log = '';
		$w = explode('{log}',$s);
		$s = $w[0];
		if (count($w) > 1) {
			$log = $w[1];
		}
		return $log;
	}

	/**
	 * log képzése
	 * @param string $old miniMarkdowsBody + oldlog
	 * @param string $new mimiMarkdownBody
	 * @return string $newLog
	 */
	public static function buildLog(string $old, string $new) : string {
		$oldBody = Minimarkdown::stripLog($old); 
		$log = Minimarkdown::getLog($old); 
		if ($new != $oldBody) {
			$log .= date('Y.m.d H:i:s')."\n".$oldBody."\n";
		}
		return $log;
	}

	/**
	 * miniMarkdownBody első néhány karaktere html kiszürve.
	 */
	public static function strLimit(string $s, int $length): string {
		$body = Minimarkdown::stripLog($s);
		$html = Minimarkdown::miniMarkdown($body);
		$html = str_replace('<br />',' ',$html);
		return mb_substr(strip_tags($html),0,$length);
	}



}

?>
