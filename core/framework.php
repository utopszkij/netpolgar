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
use Zend\Filter\StringToLower;

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
    /** utolsó akció hibaüzenete **/
    protected $errorMsg = '';
    
    /**
     * utolsó akció hibaüzenete 
     * @return string
     */
    public function getErrorMsg(): string {
        return $this->errorMsg;
    }
    
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
            $this->errorMsg = $filter->getErrorMsg();
            $result->errorMsg = $filter->getErrorMsg();
            return $result;
    }
    
    /**
     * get one record
     * @param int $int
     * @return object|false
     */
    public function getRecord(int $id) {
        $this->errorMsg = '';
        $table = new Table($this->tableName);
        $table->where(['id','=',$id]);
        $res = $table->first();
        $this->errorMsg = $table->getErrorMsg();
        return $res;
    }
    
    /**
     * update or insert one record, if insert then set record.id
     * @param object $record
     * @return bool
     */
    public function save(& $record): bool {
        $this->errorMsg = '';
        $table = new Table($this->tableName);
        if ($record->id == 0) {
            $table->insert($record);
            $record->id = $table->getInsertedId();
        } else {
            $table->where(['id','=',$record->id]);
            $table->update($record);
        }
        $this->errorMsg = $table->getErrorMsg();
        return ($this->errorMsg == '');
    }
    
    /**
     * delete one record
     * @param int $int
     * @return bool
     */
    public function delete(int $id) {
        $this->errorMsg = '';
        $table = new Table($this->tableName);
        $table->where(['id','=',$id]);
        $res = $table->delete();
        $this->errorMsg = $table->getErrorMsg();
        return $res;
    }
        
}

/**
 * View osztály   megjelenítés
 */
class View {
    
    public function echoHtmlPage(string $name, Params $p, string $jsName = '') {
        $this->echoHtmlHead($p);
        if ($jsName == '') {
            $jsName = $name;
        }
        if (file_exists('./templates/'.config('TEMPLATE').'/html/'.$name.'.html')) {
            $htmlName = './templates/'.config('TEMPLATE').'/html/'.$name.'.html';
        } else {
            $htmlName = './views/html/'.$name.'.html';
        }
        if (!file_exists($htmlName)) {
            echo 'Fatal error html template not found '.$htmlName; exit();
        }
        ?>
        <body ng-app="app">
         	<div ng-controller="ctrl" id="scope" style="display:none">
         		<div ng-include="'<?php echo $htmlName; ?>'"></div>
         	</div>
         	<?php $this->loadJavaScriptAngular($jsName, $p); ?>
        </body>
        <?php
        $this->echoHtmlEnd();
    }
    
    /**
     * templates elérési utvonalak betétele a $->templaes -be
     * @param Params $p
     * @param array $names
     */
    public function setTemplates(Params & $p, array $names = []) {
        $names[] = 'navbar';
        $names[] = 'footer';
        $names[] = 'popup';
        $p->templates = new stdClass();
        $p->TEMPLATE = config('TEMPLATE');
        foreach ($names as $name) {
            if (file_exists(config('MYPATH').'/templates/'.$p->TEMPLATE.'/html/'.$name.'.html')) {
                $p->templates->$name = config('MYDOMAIN').'/templates/'.$p->TEMPLATE.'/html/'.$name.'.html';
            } else {
                $p->templates->$name = config('MYDOMAIN').'/views/html/'.$name.'.html';
            }
        }
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
        global $REQUEST;
        $languages = get_defined_constants(true);
        
        function adjust($fv): string {
            $result = $fv;
            if (is_array($fv)) {
                $result = JSON_encode($fv);
            } else if (is_object($fv)) {
                $result = JSON_encode($fv);
            } else if (is_bool($fv)) {
                $result = JSON_encode($fv);
            } else if (is_string($fv)) {
                $fv = str_replace("'", "\\'", $fv);
                $fv = str_replace("\n", "\\n", $fv);
                $fv = str_replace("\r", "\\r", $fv);
                $fv = str_replace("\t", "\\t", $fv);
                $fv = str_replace('"', '\"', $fv);
                $result = '"'.$fv.'"';
            }	else {
                $result = $fv;
            }
            if ($result == '') {
                $result = '""';
            }
            return $result;
        }
        ?>
        <script src="https://code.angularjs.org/1.7.8/angular.js"></script>
        <script type="text/javascript">
        angular.module("app", []).controller("ctrl", function($scope) {
        $scope.MYDOMAIN = "<?php echo config('MYDOMAIN'); ?>";
        $scope.cookieEnabled = "<?php echo $REQUEST->sessionGet('cookieEnabled'); ?>"
        $scope.LNG = {};
        $scope.txt = function(token) {
            if ($scope.LNG[token] == undefined) {
                return token;
            } else {
                return $scope.LNG[token];
            }
        };
        <?php foreach ($languages['user'] as $fn => $fv) : ?>
            <?php if (substr($fn,0,5) != 'MYSQL')  : ?>
                  $scope.LNG.<?php echo $fn; ?> = <?php echo JSON_encode($fv); ?>;
            <?php endif; ?>
        <?php endforeach; ?> 
        <?php foreach ($params as $fn => $fv) : ?>
        	<?php if ($fn != '') : ?>
        		$scope.<?php echo $fn; ?> = <?php echo adjust($fv); ?>;
        	<?php endif ?>
        <?php endforeach; ?>
        $scope.sid = "<?php session_id(); ?>";
        <?php include_once './js/'.$jsName.'.js'; ?>
        $("#scope").show();
        $("#working").hide();
        }); // controller function
        </script>
        <?php
    }

    /**
     * echo hTML head
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
        $s = str_replace('{{DEFLNG}}',DEFLNG,$s);
        echo str_replace('{{MYDOMAIN}}',MYDOMAIN,$s);
    }

    /**
    * echo json header
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
     * make paginator object 
     * @param int $toal
     * @param int $offset
     * @param int $limit
     * @return [{offset, paegNo, enabled}, ...]
     */
    public function makePaginators(int $total, int $offset, int $limit=20): array {
        $result = [];
        $offsetLast = 0;
        $p = 1; // pageNo
        $n = 1; // enyyi elemet ír ki az aktuális körül
        $prevPageNo = '';
        if ($offset < (2*$limit)) {
            $n = 3;
        }
        if ($offset > ($total - (2*$limit))) {
            $n = 3;
        }
        for ($o = 0; $o < $total; $o = $o + $limit) {
            if (($o == 0) |
                (($o >= ($offset - ($n*$limit))) & ($o <= ($offset + ($n*$limit)))) |
                ($o >= ($total - $limit))) {
                    if ($o == $offset) {
                        $result[] = JSON_decode('{"offset":'.$o.', "pageNo":'.$p.', "enabled":false}');
                    } else {
                        $result[] = JSON_decode('{"offset":'.$o.', "pageNo":'.$p.', "enabled":true}');
                    }
                    $offsetLast = $o;
                    $prevPageNo = $p;
                } else if ($prevPageNo != '...') {
                        $result[] = JSON_decode('{"offset":'.$o.', "pageNo":"...", "enabled":false}');
                        $prevPageNo = '...';
                }
                $p = $p + 1;
        }
        return $result;
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
        $result->loggedUser = new UserRecord();
        foreach ($request->sessionGet('loggedUser', new UserRecord()) as $fn => $fv) {
            $result->loggedUser->$fn = $fv;
        }
        $result->access_token = $request->sessionGet('access_token', '');
        $result->msgs = [];
        $result->msgClass = 'danger';
        $result->DEFLNG = DEFLNG;
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
        if ($_SERVER['REMOTE_ADDR'] == '192.168.0.12') {
            // local test
            return;
        }
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
    
    /**
     * átirányítás URL -re
     * @param string $url
     */
    public function redirectTo(string $url) {
        if (!headers_sent()) {
            header('Location: '.$url);
        } else {
            echo '<script type="text/javascript">location="'.$url.'";</script>';
        }
    }
    
    /**
     * move uploaded file to target
     * @param string $postName
     * @param string $target
     * @return string - fileName in target or '';
     */
    public function getUploadedFile(string $postName, string $target): string {
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
     * Hash képzése
     * @param string $alg
     * @param string $s
     * @return string
     */
    public function myHash(string $alg, string $s) {
        return hash($alg, $s);
    }
    
    /**
     * email küldése config -ban beállított smtp kiszolgálóval
     * @param string $to
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function sendEmail(string $to, string $subject, string $body): bool {
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
    
} // class Controller


/**
 * Request osztály GET/POST input és session kezelés
 */
class Request {
    /** paraméterek */
    public $params = array();
    /** session változók */
	protected $sessions = false;

	/** konstruktor */
	function __construct() {
	    global $REQUEST;
	    $this->sessions = new stdClass();
	    $REQUEST = $this;
	}

	/**
	 * get item from $this->params
	 * @param string $name
	 * @param mixed $default
	 * @return mixed
	 */
	public function input(string $name, $default = '') {
	    $name = Strtolower($name);
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
		$this->params[strtolower($name)] = $value;
	}

	/**
	 * get item from session
	 * @param string $name
	 * @param string $default
	 * @return mixed
	 */
	public function sessionGet(string $name, $default='') {
	    $result = $default;
	    if (isset($_SESSION[$name])) {
	        $result = JSON_decode($_SESSION[$name]);
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
	    $_SESSION[$name] = JSON_encode($value);
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
        $result = $s;
    }
    return $result;
}

?>
