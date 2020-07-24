<?php
/**
 * OpenId szolgáltatás magyarorszag.hu ügyfélkapu használatával
 * @package uklogin
 * @author Fogler Tibor
 */

/**
 * TxtController osztály
 * @author utopszkij
 */
class TxtController extends Controller {
    
    function __construct() {
        $this->cName = 'txt';
    }
    
    /**
     * Új nyelvi konstans felviteli képernyő
     * @param Request $request
     */
    public function add(Request $request) {
        $token = $request->input('token');
        echo '<!doctype html>
        <html lang="hu">
        <head>
            <title>languaga constant manager</tile>
            <meta charset="utf-8">
        </head>
        <body>
        <h2>Define language constant</h2>
        <form method="post" action="'.MYDOMAIN.'/opt/txt/doadd">
            <p>langs_<input type="text" name="lngname" value="'.$request->sessionGet('lngName','').'" />_hu.php</p>
            <p>Token:<input type="text" name="token" value="'.$token.'" /></p>
            <p><input type="text" name="value" size="80"/></p>
            <p><button type="submit">SAVE</button>
        </form>
        </body>
        </html>
        ';
    }
    
    /**
     * Újy nyelvi konstans tárolása
     * @param Request $request
     */
    public function doadd(Request $request) {
        $lngName = $request->input('lngname');
        $token = $request->input('token');
        $value = $request->input('value');
        $request->sessionSet('lngName', $lngName);
        $fileName = './langs/'.$lngName.'_hu.php';
        if (file_exists($fileName)) {
            $lines = file($fileName);
        } else {
            $lines = ["<?php\n","?>\n"];
        }
        if ($lines[count($lines) - 1] == "?>\n") {
            $lines[count($lines) - 1] = 'DEFINE("'.$token.'","'.$value.'");'."\n";
            $lines[] = "?>\n";
        } else {
            $lines[count($lines) - 2] = 'DEFINE("'.$token.'","'.$value.'")'."\n";
            $lines[count($lines) - 1] = '?>';
        }
        $fp = fopen($fileName,'w+');
        fwrite($fp, implode("",$lines));
        fclose($fp);
        chmod($fileName, 0777);
        echo '<!doctype html>
        <html lang="hu">
        <head>
            <title>Language contant manager</title>
            <meta charset="utf-8">
        </head>
        <body>
        <h2>Language constant saved</h2>
        </body>
        </html>
        ';
    }
}
?>