<?php
include_once './controllers/common.php';
class IssuController extends CommonController {
	
    /**
     * issu beköldő form kirajzolása
     * @param object $request - title, body, sender, email
     * @return void
     */
    public function form(RequestObject $request) {
        $data = $this->init($request, 'issu');
        $data->msgs = [];
        $data->issu = new IssuRecord();
        $data->issu->title = $request->input('title');
        $data->issu->body = $request->input('body');
        if ($data->user->id > 0) {
            $data->issu->sender = $request->input('sender', $data->user->name);
            $data->issu->email = $request->input('email', $data->user->email);
        } else {
            $data->issu->sender = $request->input('sender', '');
            $data->issu->email = $request->input('email', '');
        }
        $this->view->form($data);
    }
    
    /**
     * issu beküldése
     * @param object $request - title, body, sender, email
     * @retiurn void
     */
    public function send(RequestObject $request) {
        $data = $this->init($request, 'issu');
        $data->msgs = [];
        $data->issu = new IssuRecord();
        $data->issu->title = $request->input('title');
        $data->issu->body = $request->input('body');
        $data->issu->sender = $request->input('sender');
        $data->issu->email = $request->input('email');
        $data->msgs = $this->model->check($data->issu);
        if (count($data->msgs) == 0) {
            $data->msgs = $this->model->send($data->issu);
            $this->view->successMsg(['ISSU_SAVED'],'','',$data);
        } else {
            $this->view->form($data);
        }
    }
    
}
?>