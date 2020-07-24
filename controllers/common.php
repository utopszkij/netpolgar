<?php
class CommonController extends Controller {
    public $model;
    public $view;
    protected $cName = '';
    
    /**
     * konvertálás stdClass --> UserRecord
     * @param object $obj
     * @return UserRecord
     */
    public function objToUserRecord($obj): UserRecord {
        $result = new UserRecord();
        foreach ($result as $fn => $fv) {
            if (is_object($obj)) {
                if (isset($obj->$fn)) {
                    $result->$fn = $obj->$fn;
                } else {
                    $result->$fn = $fv;
                }
            }
        }
        return $result;
    }
    
    /**
     * standert controller inicializálás
     * @param Request $request
     * @param string $name - controller name
     * @return object {"user": UserRecord, "loggedUser": UserRecord, userAdmin:bool, avtarUrl: string}
     */
    public function init(Request &$request, array $names = []): Params {
        $p = parent::init($request, $names);
        $user = $request->sessionGet('loggedUser', new UserRecord());
        $name = $request->input('option','none');
        if (file_exists('./models/'.$name.'.php')) {
            $this->model = $this->getModel($name);
        } else {
            $this->model = new Model();
        }
        if (file_exists('./views/'.$name.'.php')) {
            $this->view = $this->getView($name);
        } else {
            $this->view = new View();
        }
        
        if ($user->avatar == '') {
            $user->avatar = './images/noavatar.png';
        }
        $p->loggedUser = new UserRecord();
        foreach ($user as $fn => $fv) {
            $p->loggedUser->$fn = $fv;            
        }
        if ($name == 'user') {
            $p->userAdmin = $this->model->isAdmin($p->loggedUser->id);
        } else {
            $userModel = $this->getModel('users');
            $p->userAdmin = $userModel->isAdmin($p->loggedUser->id);
        }
        if ($p->loggedUser->avatar == 'gravatar') {
            $p->avatarUrl = 'https://gravatar.com/avatar/'.md5($p->loggedUser->email);
        } else {
            $p->avatarUrl = $p->loggedUser->avatar;
        }
        return $p;
    }
    
}
?>