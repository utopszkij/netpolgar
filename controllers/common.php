<?php
class CommonController extends Controller {
    public $model;
    public $view;
    
    /**
     * konvertálás stdClass --> UserRecord
     * @param object $obj
     * @return UserRecord
     */
    public function objToUserRecord($obj): UserRecord {
        $result = new UserRecord();
        foreach ($obj as $fn => $fv) {
            $result->$fn = $fv;
        }
        return $result;
    }
    
    /**
     * standert controller inicializálás
     * @param Request $request
     * @param string $name - controller name
     * @return object {"user": UserRecord, userAdmon:bool, avtarUrl: string}
     */
    public function init(Request &$request, array $names = []): Params {
        $p = parent::init($request, $names);
        $user = $this->objToUserRecord($request->sessionGet('loggedUser', new UserRecord));
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
        $p->loggedUser = $user;
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
    
    /**
     * átirányitás url -re
     * @param string $url
     */
    public function redirect(string $url) {
        redirectTo($url);
    }
}
?>