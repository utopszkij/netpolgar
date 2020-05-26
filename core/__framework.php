<?php
/**
 * MVC adbsztarkt osztályok, http GET/POST és session kezelő osztály,
 * álltalános célú segéd rutinok
 */

global $REQUEST;

interface ModelObject {
    
}

interface ViewObject {
    public function echoHtmlPopup();
} 

interface ControllerObject {
}

interface  RequestObject {
    public function input(string $name, $default = '');
    public function set(string $name, $value);
    public function sessionGet(string $name, $default='');
    public function sessionSet(string $name, $value);
    public function session_count(): int;
}

class Model implements ModelObject {
    
}

class View implements ViewObject {
    /**
     * echo javascript code, inject params
     * @param string $jsName javascript file full path
     * @param array $params  {"name":value, ....}
     * @return void
     */
    protected function loadJavaScript(string $jsName, $params) {
    	echo "\n".'<script type="text/javascript">'."\n";
    	echo '// params from controller'."\n";
    	foreach ($params as $fn => $fv) {
    		if ($fn != '') {
    			if (is_array($fv)) {
    				echo "var $fn = ".JSON_encode($fv).";\n";
    			} else if (is_object($fv)) {
    				echo "var $fn = ".JSON_encode($fv).";\n";
    			} else if (is_string($fv)) {
    				$fv = str_replace("'", "\\'", $fv);
    				$fv = str_replace("\n", "\\n", $fv);
    				$fv = str_replace("\r", "\\r", $fv);
    				$fv = str_replace("\t", "\\t", $fv);
    				echo "var $fn = '$fv;\n";
    			} else if ($fv === false) {
    			    echo "var $fn = false\n";
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
        <script src="./core/angular.js"></script>
        <!-- script src="https://code.angularjs.org/1.7.8/angular.js"></script -->
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
            echo '$scope.MYDOMAIN="'.MYDOMAIN.'";'."\n";
            foreach ($params as $fn => $fv) {
                if ($fn != '') {
                    if (is_array($fv)) {
                        echo '$scope.'."$fn = ".JSON_encode($fv).";\n";
                    } else if (is_object($fv)) {
                        echo '$scope.'."$fn = ".JSON_encode($fv).";\n";
                    } else if (is_string($fv)) {
                        $fv = str_replace("'", "\\'", $fv);
                        $fv = str_replace("\n", "\\n", $fv);
                        $fv = str_replace("\r", "\\r", $fv);
                        $fv = str_replace("\t", "\\t", $fv);
                        echo '$scope.'."$fn = '$fv';\n";
                    } else if ($fv === false) {
                        echo "var $fn = false\n";
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
     * must use htmlPopup() in HTML body tag
     * @return void
     */
    public function echoHtmlHead($data = '') {
        $lines = file('./templates/'.config('TEMPLATE').'/htmlhead.html');
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
    
} // class View

class Controller  implements ControllerObject {
    
    /**
     * create new model object from "./models/modelname.php"
     * @param string $modelName
     * @return Model
     */
    protected function getModel(string $modelName) {
        include_once './models/'.$modelName.'.php';
        $className = ucfirst($modelName).'Model';
        return new $className ();
    }
    
    /**
     * cretae new view object from "./views/viewName.php" or "./views/viewName_lng.php" 
     * @param string $viewName
     * @return View
     */
    protected function getView(string $viewName) {
        global $REQUEST;
        $className = ucfirst($viewName).'View';
        include_once './views/'.$viewName.'.php';
        return new $className ();
    }
    
    /**
     * Create new csrToken tárol sessionba és $data -ba
     * @param Request $request
     * @param object $data
     * @return void
     */
    protected function createCsrToken(&$request, &$data) {
        $request->sessionSet('csrToken', md5(random_int(1000000,9999999)));
        $data->csrToken = $request->sessionGet('csrToken','');
    }
    
    /**
     * $request -ben érkező csrToken ellenörzése
     * @param Request $request
     * @return void
     */
    protected function checkCsrToken(&$request) {
        if ($request->input($request->sessionGet('csrToken')) != 1) {
            echo '<p>invalid csr token</p> sessionban csrToken='.$request->sessionGet('csrToken','?').
            ' inputban='.$request->input($request->sessionGet('csrToken'),'??').' '.__FILE__;
            exit();
        }
    }
    
    public function redirect(string $url) {
        if (headers_sent()) {
            echo '<script type="text/javascript">
                    // redirect
                    location="'.$url.'";
                  </script>
            ';  
        } else {
            header('Location: '.$url);
        }
    }
} // class Controller


class Request implements RequestObject {
	public $params = array();
	protected $sessions = false;
	
	function __construct() {
	    global $REQUEST;
	    $this->sessions = false;
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
		 if (!is_object($this->sessions)) {
			$this->sessions = new stdClass();		 
		 }	    
	    $this->sessions->$name = $value;
	    $this->session_save($sessionId);
	}
	
	/**
	 * session_start - open sessions record from database or create new
	 * @param string $sessionId
	 * @return void
	 */
	protected function session_init(string $sessionId) {
	    $maxlifetime = ini_get("session.gc_maxlifetime");
	    if (!$this->sessions) {
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
	    $table->where(array('id',$sessionId));
	    return $table->count();
	}
} // Request

function defineIfNotExists(string $name, $value) {
    if (!defined($name)) {
        define($name,$value);
    }
}

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
 * language convertion
 * @param string $s language token
 * @return string translated text
 */
function txt(string $s): string {
    $result = $s;
    if (defined($s)) {
        $result = constant($s);
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

?>