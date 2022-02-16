<?php 
/**
* minimarkdown to html konverter
* értelmezve: 
*      http......     link
*      ![](http...)   kép url
*      **...**        kiemelt szöveg
*      *....*  dölt betüs szöveg
*      :(  :)  :|     emotions 
*/

/**
* távoli file infok lekérdezése teljes letöltés nélkül
* csak 'http' -vel kezdödő linkeket ellenöriz
* @param string $url
* @return array ['fileExist', 'fileSize' ]
*/
function getRemoteFileInfo($url) {
		if (substr($url,0,4) == 'http') {
		   $ch = curl_init($url);
		   curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		   curl_setopt($ch, CURLOPT_HEADER, TRUE);
		   curl_setopt($ch, CURLOPT_NOBODY, TRUE);
		   $data = curl_exec($ch);
		   $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
		   $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		   curl_close($ch);
		   $result = [
	        'fileExists' => (int) $httpResponseCode == 200,
	        'fileSize' => (int) $fileSize
		   ];
		} else {
		   $result = [
	        'fileExists' => 1,
	        'fileSize' => 100
		   ];
		}
		return $result;
}

function str_replace_first($search, $replace, $subject)
{
    $search = '/'.preg_quote($search, '/').'/';
    return preg_replace($search, $replace, $subject, 1);
}

/**
 * mini markdown parser
 * @param markdown string $s
 * @return html string
 */
function miniMarkdown($s) {
	 $s = strip_tags($s,['br<img src="homokozo.png" alt="" /><img src="homokozo.png" alt="" />']);
    $s = str_replace("\r\n",'<br />',$s);       // \r\n --> <br />
    $s = str_replace("\n",'<br />',$s);         // \n --> <br />
    $s = str_replace("\r",'<br />',$s);         // \r --> <br />
    $imgs = [];
	 preg_match_all('~!\[\]\([^\s<]+\)~',$s, $imgs);
	 // ne legyen több mint 3 kép
	 for ($i=3; $i<count($imgs[0]); $i++) {
	 		$s = str_replace_first($imgs[0][$i],'',$s);
	 }		 
    $imgs = [];
	 preg_match_all('~!\[\]\([^\s<]+\)~',$s, $imgs);
	 // túl bagy képek törlése	
	 foreach ($imgs[0] as $img) {
		 	$imgUrl = str_replace('![](','',$img);
		 	$imgUrl = str_replace(')','',$imgUrl);
		 	$fileInfo = getRemoteFileInfo($imgUrl);
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
    

	 // emotions
    $s = str_replace(':)','<em class="fas fa-grin"></em>',$s);
    $s = str_replace(':(','<em class="fas fa-frown"></em>',$s);
    $s = str_replace(':|','<em class="fas fa-flushed"></em>',$s);
    return $s;
}

?>