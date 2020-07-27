<?php
interface ModelObject {
    
}

interface ViewObject {
}

interface ControllerObject {
}

interface  RequestObject {
    public function input(string $name, $default = '');
    public function set(string $name, $value);
    public function sessionGet(string $name, $default='');
    public function sessionSet(string $name, $value);
    public function session_init(string $sessionId);
    public function session_save(string $sessionId);
    public function session_count(): int;
}

class Params {
    public $loggedUser = false;
}

class View implements ViewObject {
    public function loadJavaScript(string $jsName, object $params) {
    }
    
    public function loadJavaScriptAngular(string $jsName, object $params) {
        echo '<scryp>';
        foreach ($params as $fn => $fv) {
            echo $fn.'='.JSON_encode($fv).';'."\n";
        }
        echo '</scrypt>';
    }
    
    public function echoHtmlHead() {
    }
    
    public function echoHtmlPopup() {
    }
    
    public function echoLngHtml(string $htmlName, $p) {
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
    
    public function errorMsg(array $msgs, string $backLink, string $backStr, $p) {
        echo JSON_encode($msgs);
    }
    
    public function successMsg(array $msgs, string $backLink, string $backStr, Params $p) {
        echo JSON_encode($msgs);
    }
    

}

class Model implements ModelObject {
    
}

class Controller implements ControllerObject {
    
    protected $cName = '';
    public $model = false;
    public $view = false;
    
    protected function getModel(string $modelName)  {
        if (file_exists('./models/'.$modelName.'.php')) {
            include_once './models/'.$modelName.'.php';
            $modelClassName = $modelName.'Model';
            return new $modelClassName ();
        } else {
            return false;
        }
    }
    
    protected function getView(string $viewName) {
        if (file_exists('./views/'.$viewName.'.php')) {
            $viewClassName = $viewName.'View';
            include_once './views/'.$viewName.'.php';
            return new $viewClassName ();
        } else {
            return false;
        }
    }
    
    protected function createCsrToken(RequestObject $request, object $data) {
        $request->sessionSet('csrToken','testCsrToken');
        $data->csrToken = 'testCsrToken';
    }
    
    protected function checkCsrToken(RequestObject $request) {
        if ($request->input($request->sessionget('csrToken')) != 1) {
            echo '<p>invalid csr token</p>'.JSON_encode($request);
            exit();
        }
    }

    protected function docPage(RequestObject $request, string $viewName) {
        $request->set('sessionid','0');
        $request->set('lng','hu');
        $view = $this->getView($viewName);
        $data = new stdClass();
        $data->option = $request->input('option','default');
        $data->adminNick = $request->sessionGet('adminNick','');
        $view->display($data);
    }
    
    public function redirect(string $url) {
        echo 'redirect='.$url;
    }
    
    public function init(Request &$request, array $names = []): Params {
        $result = new Params();
        if ($this->cName != '') {
            $name = $this->cName;
        } else {
            $name = $request->input('option','none');
        }
         
        $this->model = $this->getModel($name);
        $this->view = $this->getView($name);
        $result->loggedUser = $request->sessionGet('loggedUser', JSON_decode('{"id":0, "nick":"guest", "avatar":""}'));
        foreach ($names as $fn) {
            $result->$fn = $request->input($fn,'');
        }
        return $result;
    }
} // class Controller

class Request implements RequestObject {
    protected $sessions;
    function __construct() {
        $this->sessions = new stdClass();
    }
    public function set(string $name, $value) {
        $this->$name = $value;
    }
    public function input(string $name, $def='') {
        $result = $def;
        if (isset($this->$name)) $result = $this->$name;
        return $result;
    }
    public function sessionSet(string $name,$value) {
        $this->sessions->$name = $value;
    }
    public function sessionGet(string $name, $def = '') {
        $result = $def;
        if (isset($this->sessions->$name)) $result = $this->sessions->$name;
        return $result;
    }
    public function session_count(): int {
        return 1;
    }
    public function session_init(string $sessionId): int {
        $this->sessions = new stdClass();
    }
    public function session_save(string $sessionId) {
    }
}

function txt(string $s): string {
    return $s;
}

/**
 * A "tests" könyvtárból másol a terget -be
 * postname -től függetlenül mindig ugyanazt a file-t.
 * @param string $postName
 * @param string $target
 * @return string
 */
function getUploadedFile(string $postName, string $targetDir): string {
    $name = 'avdhA3-18840f38-7adf-4f8a-a8b2-c3e307d63b48.pdf';
    if (file_exists('./tests/'.$name)) {
        if (copy ('./tests/'.$name, $targetDir.'/'.$name)) {
            $result = $name;
        } else {
            $result = '';
        }
    } else {
        $result = '';
    }
    return $result;
}

?>