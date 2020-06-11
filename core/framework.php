<?php
/**
 * OpenId szolgáltatás magyarorszag.hu ügyfélkapu használatával
 * Framework
 * @package uklogin
 * @author Fogler Tibor
 * MVC adbsztarkt osztályok, http GET/POST és session kezelő osztály,
 * álltalános célú segéd rutinok
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/** global Request object */
global $REQUEST;
/** global Params objekt */
global $PARAMS;
/**created models **/
global $MODELS;
$MODELS = new stdClass();

/**
 * Params osztály Paraméterek a view taskok számára
 */
class Params {
    /** array of string, nyelvi konstansok */
    public $msgs = [];
    /** UserRecord */
    public $loggedUser = false;
    /** string userinfo lekérdezéshez */
    public $access_token = '';
    /** Model object */
    public $model = false;
    /** View object */
    public $view = false;

    /**
     * constructor
     */
    function __construct() {
        global $PARAMS;
        $PARAMS = $this;
    }
}

/**
 * $model->getRecords taskok kimeneti objektum osztálya
 */
class GetRecordsResult {
    /** a feltételeknek megfelelő összes rekord (offset, limit figyelmen kivül hagyásával) */
    public $total = 0;
    /** eredmény rekordokat tartalmazó tőmb */
    public $items = [];
    /** sql error vagy '' */
    public $errorMsg = '';
}

/**
 * adatmodell osztály
 */
class Model {
    /** tábla név */
    protected $tableName = '';
    /** filterStr megadása esetén ebben a mezőben keres */
    protected $filterField = '';

    /**
     * Rekord készlet olvasása gyakran átirandó ennek a mintájára
     * @param int $offset
     * @param int $limit
     * @param string $order
     * @param string $orderDir
     * @param string $filterStr
     * @return GetRecordsResult, és $this->errorMsg beállítva
     */
    public function getRecords(int $offset, int $limit,
        string $order, string $orderDir, string $filterStr):GetRecordsResult {
            $this->errorMsg = '';
            $filter = new Filter($this->tableName,'u');
            $filter->setColumns('u.*');
            if ($filterStr != '') {
                $filter->where([$this->filterField,'like','%'.$filterStr.'%']);
            }
            $filter->offset($offset);
            $filter->limit($limit);
            $filter->order($order.' '.$orderDir);
            $result = new GetRecordsResult();
            $result->total = $filter->count();
            $result->items = $filter->get();
            $result->errorMsg = $filter->getErrorMsg();
            return $result;
    }
}

/**
 * View osztály   megjelenítés
 */
class View {
    /**
     * echo javascript code, inject params
     * @param string $jsName javascript file full path
     * @param array $params  {"name":value, ....}
     * @return void
     */
    protected function loadJavaScript(string $jsName, Params $params) {
    	echo "\n".'<script type="text/javascript">'."\n";
    	echo '// params from controller'."\n";
    	foreach ($params as $fn => $fv) {
    		if ($fn != '') {
    			if (is_array($fv)) {
    				echo "var $fn = ".JSON_encode($fv).";\n";
    			} else if (is_object($fv)) {
    				echo "var $fn = ".JSON_encode($fv).";\n";
    			} else if (is_bool($fv)) {
    			    echo "var $fn = ".JSON_encode($fv).";\n";
    			} else if (is_string($fv)) {
    				$fv = str_replace("'", "\\'", $fv);
    				$fv = str_replace("\n", "\\n", $fv);
    				$fv = str_replace("\r", "\\r", $fv);
    				$fv = str_replace("\t", "\\t", $fv);
    				echo "var $fn = '$fv';\n";
    			}	else {
    				echo "var $fn = $fv;\n";
    			}
    		}
    	}
    	echo 'var sid = "'.session_id().'";'."\n";
    	include_once './js/'.$jsName.'.js';
    	echo '$("#working").hide()'."\n";
    	echo "\n".'</script>'."\n";
    }

    /**
     * echo javascript code, inject params and language constanses
     * must in html:  <body ng-app="app">
     *                  <div ng-controller="ctrl">
     *                      ....
     *                      <?php loadJavaSciptAngular('jsName', $params); ?>
     *                  </div>
     *                </body>
     * @param string $jsName javascript file full path
     * @param array $params  {"name":value, ....}
     * @return void
     */
    public function loadJavaScriptAngular(string $jsName, $params) {
        ?>
        <script src="https://code.angularjs.org/1.7.8/angular.js"></script>
        <script type="text/javascript">
        angular.module("app", []).controller("ctrl", function($scope) {
            <?php
            $languages = get_defined_constants(true);
            echo '$scope.LNG = [];'."\n";
            foreach ($languages['user'] as $fn => $fv) {
                if (substr($fn,0,5) != 'MYSQL') {
                    echo '$scope.LNG["'.$fn.'"] = '.JSON_encode($fv).';'."\n";
                }
            }
            echo '$scope.txt = function(token) {
                if ($scope.LNG[token] == undefined) {
                    return token;
                } else {
                    return $scope.LNG[token];
                }
            };
            ';
            foreach ($params as $fn => $fv) {
                if ($fn != '') {
                    if (is_array($fv)) {
                        echo '$scope.'."$fn = ".JSON_encode($fv).";\n";
                    } else if (is_object($fv)) {
                        echo '$scope.'."$fn = ".JSON_encode($fv).";\n";
                    } else if (is_bool($fv)) {
                        echo '$scope.'."$fn = ".JSON_encode($fv).";\n";
                    } else if (is_string($fv)) {
                        $fv = str_replace("'", "\\'", $fv);
                        $fv = str_replace("\n", "\\n", $fv);
                        $fv = str_replace("\r", "\\r", $fv);
                        $fv = str_replace("\t", "\\t", $fv);
                        echo '$scope.'."$fn = '$fv';\n";
                    }	else {
                        echo '$scope.'."$fn = $fv;\n";
                    }
                }
            }
            echo '$scope.sid = "'.session_id().'";'."\n";
            include_once './js/'.$jsName.'.js';
            ?>
            $("#scope").show();
            $("#working").hide();
        }); // controller function
        </script>

        <?php
    }

    /**
     * return hTML head
     *    include javascript global.alert, global.confirm, global.post, globa.working functions
     * figyelem használni kell a htmlPopup() hívást a HTML body -ban
     * A ./templates/.TEMPLATE.'/htmlhead.html' -t használja
     * @param Params $data
     * @return void
     */
    public function echoHtmlHead(Params $data) {
        $lines = file('./templates/'.TEMPLATE.'/htmlhead.html');
        $s = implode("\n",$lines);
        if (is_object($data)) {
            if ((isset($data->extraCss)) && ($data->extraCss != '')) {
                $extraCss = '<link rel="stylesheet" href="'.$data->extraCss.'">';
            } else {
                $extraCss = '<!-- extraCss -->';
            }
        } else {
            $extraCss = '<!-- extraCss -->';
        }
        $s = str_replace('{{EXTRACSS}}',$extraCss,$s);
        echo str_replace('{{MYDOMAIN}}',MYDOMAIN,$s);
    }

    /**
    * json header
    */
    public function echoJsonHead() {
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
    }

    /**
     * echo html end
     */
    public function echoHtmlEnd() {
        echo '</html>'."\n";
    }


    /**
     * echo popup html code (use this HTML global.alert, global.confirm functions in htmlHead)
     * @return void
     */
    public function echoHtmlPopup() {
        echo '
        <div id="popup" style="display:none">
            <p class="alert alert-danger"></p>
            <div id="popupButtons">
                <button type="button" id="popupYes" class="btn btn-primary">
                    <em class="fa fa-check"></em>
                    '.txt('YES').'
                </button>
    			<button type="button" id="popupNo" class="btn btn-danger">
                    <em class="fa fa-ban"></em>
                    '.txt('NO').'
                </button>
    			<button type="button" id="popupClose">'.txt('CLOSE').'</button>
    		</div>
        </div>
        <div id="working"><span>'.txt('WORKING').'...</span></div>
    	';
    }

    /**
    * nyelv függő fregment (html kód részlet) betöltése az langs/htmlName_lng.html fileból
    * @param string $htmlName filename (only name, not include path and lng code and extension)
    * @param object params
    */
    public function echoLngHtml(string $htmlName,$p) {
    	global $REQUEST;
    	$lng = $REQUEST->sessionget('lng','hu');
    	if (file_exists('langs/'.$htmlName.'_'.$lng.'.html')) {
    		include 'langs/'.$htmlName.'_'.$lng.'.html';
    	} else if (file_exists('langs/'.$htmlName.'.html')) {
    		include 'langs/'.$htmlName.'.html';
    	} else {
			echo '<p>'.$htmlName.' html file not found.</p>';
    	}
    }

    /**
     * Nyelvi konstansok átadása a JS .nek
     * @param array $tokens átadandó tokenek
     */
    public function echoJsLngDefs(array $tokens) {
        echo "\n<!-- language constaints -->\n";
        echo '<script type="text/javascript">'."\n";
        echo '  global.LNG = {};'."\n";
        foreach ($tokens as $token) {
            echo '  global.LNG.'.$token.' = "'.txt($token).'";'."\n";
        }
        echo '</script>'."\n";
    }

} // class View

/**
 * Controller osztály  üzleti logika
 * @author utopszkij
 */
class Controller {

    /** controller neve */
    protected $cName = '';

    /**
     * konstruktor
     */
    function __construct() {
        global $REQUEST;
        $this->cName = $REQUEST->input('option','');
    }

    /**
     * task init - logged User és kapott paraméterek a result Params -ba
     * model és view létrehozása
     * @param Request $request
     * @param array $names elvért paraméter nevek
     * @return Params
     */
    protected function init(Request &$request, array $names = []): Params {
        $this->model = $this->getModel($this->cName);
        $this->view = $this->getView($this->cName);
        $result = new Params();
        // tényleges érkezett paraméterek
        foreach ($request->params as $fn => $fv) {
            $result->$fn = $fv;
        }
        // az elvárt, de nem érkezett paraméterek '' értékkel
        foreach ($names as $name) {
            if (!isset($result->$name)) {
                $result->$name = '';
            }
        }
        $result->loggedUser = $request->sessionGet('loggedUser', false);
        $result->access_token = $request->sessionGet('access_token', '');
        return $result;
    }

    /**
     * create new model object from "./models/modelname.php"
     * @param string $modelName
     * @return Model
     */
    protected function getModel(string $modelName) {
        global $MODELS;
        if (isset($MODELS->$modelName)) {
            return $MODELS->$modelName;
        }
        if (file_exists('./models/'.$modelName.'.php')) {
            include_once './models/'.$modelName.'.php';
            $className = ucfirst($modelName).'Model';
            $MODELS->$modelName = new $className ();
            return $MODELS->$modelName;
        } else {
            return new stdClass();
        }
    }

    /**
     * cretae new view object from "./views/viewName.php" or "./views/viewName_lng.php"
     * @param string $viewName
     * @return View
     */
    protected function getView(string $viewName) {
        if (file_exists('./views/'.$viewName.'.php')) {
            $className = ucfirst($viewName).'View';
            include_once './views/'.$viewName.'.php';
            return new $className ();
        } else {
            return new stdClass();
        }
    }

    /**
     * Create new csrToken tárol sessionba és $data -ba
     * @param Request $request
     * @param object $data
     * @return void
     */
    protected function createCsrToken(&$request, &$data) {
        $request->sessionSet('csrToken', 'a'.md5(random_int(1000000,9999999)));
        $data->csrToken = $request->sessionGet('csrToken','');
    }

    /**
     * $request -ben érkező csrToken ellenörzése
     * @param Request $request
     * @return void
     */
    protected function checkCsrToken(&$request) {
        if ($request->input($request->sessionGet('csrToken','?'),'nincs') != 1) {
            echo '<p>invalid csr token</p> sessionban csrToken='.$request->sessionGet('csrToken','?').
            ' inputban='.$request->input($request->sessionGet('csrToken','?'),'nincs').' '.__FILE__;
           exit();
        }
    }
    /**
     * echo statikus page
     * @param Request $request
     * @param string $viewName
     */
    protected function docPage(Request $request, string $viewName) {
        $data = $this->init($request, []);
        $request->set('sessionid','0');
        $request->set('lng','hu');
        $view = $this->getView($viewName);
        $data->option = $request->input('option','default');
        $data->adminNick = $request->sessionGet('adminNick','');
        $data->access_token = $request->sessionGet('access_token','');
        $view->display($data);
    }

    /**
     * session váltás ha szükséges
     * @param string $si  (access_token)
     * @param Request $request
     */
    protected function sessionChange(string $si, Request &$request) {
        if ($si != session_id()) {
            session_abort();
            session_id($si);
            $request = new Request();
        }
    }

    /**
     * alap böngésző task, gyakran ennek mintájára átirandó:
     * public function myList(Request $request) {
     *      $this->browser($request,["formTitle" => "...", ...]);
     * }
     * szükség van a  ./core/browser includra
     * @param Request $request - option, group, offset, limit, order, order_dir, searchstr
     * @param array $options ['formTitle' => '', 'formSubTitle' => '', 'formHelp' => '',
     *    'itemTask' => '', 'addTask' => '']
     * @return void
     */
    protected function browser(Request $request, array $options) {
        include_once './core/browser.php';
        $p = $this->init($request, ['option','offset','limit','order','order_dir',
            'search_str']);
        foreach ($options as $fn => $fv) {
            $p->$fn = $fv;
        }
        $this->view = new BrowserView();
        $this->createCsrToken($request, $p);
        $p->offset = $request->input('offset', $request->sessionGet($p->option.'Offset',0));
        $p->limit = $request->input('limit', $request->sessionGet($p->option.'Limit',20));
        $p->order = $request->input('order', $request->sessionGet($p->option.'Order','kerdes'));
        $p->order_dir = $request->input('order_dir', $request->sessionGet($p->option.'Order_dir','ASC'));
        $p->searchstr = $request->input('searchstr', $request->sessionGet($p->option.'Searchstr',''));
        $records = $this->model->getRecords($p->offset, $p->limit, $p->order, $p->order_dir, $p->searchstr);
        $p->total = $records->total;
        $p->items = $records->items;
        if ($records->errorMsg != '') {
            $p->error->msgs[] = $records->errorMsg;
        }
        $request->sessionSet($p->option.'Offset', $p->offset);
        $request->sessionSet($p->option.'Limit', $p->limit);
        $request->sessionSet($p->option.'Order', $p->order);
        $request->sessionSet($p->option.'Order_dir', $p->order_dir);
        $request->sessionSet($p->option.'Searchstr', $p->searchstr);
        $this->view->listForm($p);
    }
} // class Controller


/**
 * Request osztály GET/POST input és session kezelés
 */
class Request {
    /** paraméterek */
    public $params = array();
    /** session változók */
	protected $sessions = array();

	/** konstruktor */
	function __construct() {
	    global $REQUEST;
	    $REQUEST = $this;
	}

	/**
	 * get item from $this->params
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function input(string $name, $default = '') {
		$result = $default;
		if (isset($this->params[$name])) {
			$result = $this->params[$name];
		}
		return $result;
	}

	/**
	 * set item into $this->params
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function set(string $name, $value) {
		$this->params[$name] = $value;
	}

	/**
	 * get item from session
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public function sessionGet(string $name, $default='') {
	    $result = $default;
	    $sessionId = session_id();
	    $this->session_init($sessionId);
	    if (isset($this->sessions->$name)) {
	        $result = $this->sessions->$name;
	    }
	    return $result;
	}

	/**
	 * set item into session
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function sessionSet(string $name, $value) {
	    $sessionId = session_id();
	    $this->session_init($sessionId);
	    if (is_object($this->sessions)) {
	    	$this->sessions->$name = $value;
	    	$this->session_save($sessionId);
	    }	else {
         $this->sessions = new stdClass();
	    	$this->sessions->$name = $value;
	    	$this->session_save($sessionId);
	    }
	}

	/**
	 * session_start - open sessions record from database or create new
	 * @param string $sessionId
	 * @return void
	 */
	protected function session_init(string $sessionId) {
	    $maxlifetime = ini_get("session.gc_maxlifetime");
	    if (count($this->sessions) <= 0) {
	        $this->sessions = new stdClass();
	        $db = new DB();
	        $db->statement('CREATE TABLE IF NOT EXISTS sessions (id varchar(256), data text, time datetime)');
	        $table = DB::table('sessions');
	        $table->where(array('time','<', date('Y-m-d H:i:s', (time() - $maxlifetime))))->delete();
	        $table = DB::table('sessions');
	        $res = $table->where(array('id',$sessionId))->first();
	        if ($res) {
	            $this->sessions = JSON_decode($res->data);
	            $record = new stdClass();
	            $record->time = date('Y-m-d H:i:s');
	            $table->where(array('id',$sessionId))->update($record);
	        } else {
	            $this->sessions = new stdClass();
	            $record = new stdClass();
	            $record->id = $sessionId;
	            $record->data = JSON_encode($this->sessions);
	            $record->time = date('Y-m-d H:i:s');
	            $table->insert($record);
	        }
	    } else {
	        $table = DB::table('sessions');
	        $record = new stdClass();
	        $record->time = date('Y-m-d H:i:s');
	        $table->where(array('id',$sessionId))->update($record);
	        $table->where(array('time','<', date('Y-m-d H:i:s', (time() - $maxlifetime))))->delete();
	    }
	}

	/**
	 * save session into database
	 * @param string $sessionId
	 * @return void
	 */
	protected function session_save(string $sessionId) {
	    $maxlifetime = ini_get("session.gc_maxlifetime");
	    $db = new DB();
	    $db->statement('CREATE TABLE IF NOT EXISTS sessions (id varchar(256), data text, time datetime)');
	    $table = DB::table('sessions');
	    $record = new stdClass();
	    $record->data = JSON_encode($this->sessions);
	    $record->time = date('Y-m-d H:i:s');
	    $table->where(array('id',$sessionId))->update($record);
	    $table = DB::table('sessions');
	    $table->where(array('time','<', date('Y-m-d H:i:s', (time() - $maxlifetime))))->delete();
	}

	/**
	 * count of active sessions
	 * @return integer
	 */
	public function session_count(): int {
	    $sessionId = session_id();
	    $table = DB::table('sessions');
	    $table->where(['id','=',$sessionId]);
	    return $table->count();
	}
} // Request

/**
 * url kiegészités
 * 'https(s):xxxxxxxx' változatlan
 * './xxxxxx' --> https(s)://{domain}xxxxxx
 * 'xxxxxxx' --> https(s):xxxxxxxx
 * @param string $s
 * @return string
 */
function url(string $s): string {
    $result = $s;
    if (substr($result,0,2) == './') {
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] != '') {
                $result = 'https://'.$_SERVER['SERVER_NAME'].substr($result,2,500);
            } else {
                $result = 'http://'.$_SERVER['SERVER_NAME'].substr($result,2,500);
            }
        } else {
            $result = 'http://'.$_SERVER['SERVER_NAME'].substr($result,2,500);
        }
    } else if (strpos($result,'http') === false) {
        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] != '') {
                $result = 'https:'.$result;
            } else {
                $result = 'http:'.$result;
            }
        } else {
            $result = 'http:'.$result;
        }
    }
    return $result;
}

/**
 * Nyelvi fordítás
 * @param string $s nyelvi token
 * @return string szöveg
 */
function txt(string $s): string {
    $result = $s;
    if (defined($s)) {
        $result = constant($s);
    } else {
        $result = '<a style="color:red" target="_new"
            href="'.MYDOMAIN.'/opt/txt/add/?token='.urlencode($s).'">'.$s.'</a>';
    }
    return $result;
}

/**
 * move uploaded file to target
 * @param string $postName
 * @param string $target
 * @return string - fileName in target or '';
 */
function getUploadedFile(string $postName, string $target): string {
    if (isset($_FILES[$postName])) {
        $fileName = $_FILES[$postName]['name'];
        if (move_uploaded_file($_FILES[$postName]["tmp_name"], $target.'/'.$fileName)) {
            $result = $fileName;
        } else {
            $result = '';
        }
    } else {
        $result = '';
    }
    return $result;
}

/**
 * átirányítás URL -re
 * @param string $url
 */
function redirectTo(string $url) {
    if (!headers_sent()) {
        header('Location: '.$url);
    } else {
        echo '<script type="text/javascript">location="'.$url.'";</script>';
    }
}

/**
 * Hash képzése
 * @param string $alg
 * @param string $s
 * @return string
 */
function myHash(string $alg, string $s) {
    return hash($alg, $s);
}

/**
 * email küldése config -ban beállított smtp kiszolgálóval
 * @param string $to
 * @param string $subject
 * @param string $body
 * @return bool
 */
function sendEmail(string $to, string $subject, string $body): bool {
    $mail = new PHPMailer(true);
    $result = true;
    // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->isSMTP();                                      // Set mailer to use SMTP
        $mail->Host = config('SMTPHOST');  // Specify main and backup SMTP servers
        $mail->SMTPAuth = true;                               // Enable SMTP authentication
        $mail->Username = config('SMTPUSER');                 // SMTP username
        $mail->Password = config('SMTPPSW');                  // SMTP password
        $mail->SMTPSecure = config('SMTPSECURE');             // Enable TLS encryption, `ssl` also accepted
        $mail->SMTPPort = config('SMTPPORT');                 // Port
        $mail->CharSet = 'utf-8';
        $mail->SMTPAuth = true;
        //Recipients
        $mail->setFrom(config('SMTPSENDER'));
        $mail->addAddress($to);     // Add a recipient

        //Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
    } catch (Exception $e) {
        echo Json_encode($mail).' '.JSON_encode($e);
        $result = false;
    }
    return $result;
}

?>
