<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\File;
use \App\Models\Upload;

class FileController extends Controller {

    protected $model = false;
    
    function __construct() {
        $this->model = new \App\Models\File();
    }
    
    /**
     * lapozható táblázatos megjelenítés
     * @param string $parentType
     * @param int $parentId
     * @param int $userId
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index(string $parentType, int $parentId, int $userId) {
        if ($userId != 0) {
            $parentType = 'users';
            $parentId = $userId;
            $parent = \DB::table('users')->where('id','='.$userId)->first();
            if (\Auth::check()) {
                $userMember = (\Auth::user()->id == $userId);
                $userAdminr = (\Auth::user()->id == $userId);
            } else {
                $userMember = false;
                $userAdmin = false;
            }
        } else {
            $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
            if (\Auth::check()) {
                $userMember = $this->model->userMember($parentType, $parentId);
                $userAdmin = $this->model->userAdmin($parentType, $parentId);
            } else {
                $userMember = false;
                $userAdmin = false;
            }
        }
        if ($parent) {
            $data = $this->model->getData($parentType, $parentId, $userId, 8);
            $result = view('file.index',[
                "data" => $data, 
                "parent" => $parent,
                "parentType" => $parentType,
                "parentId" => $parentId,
                "userId" => $userId,
                "userMember" => $userMember,
                "userAdmin" => $userAdmin
            ])->with('i', (request()->input('page', 1) - 1) * 8);
        } else {
            $result = redirect()->to('/')->with('error','Fatal error parent not found');
        }
        return $result;
    }
    
    /**
     * bejelentkezett user jogosult erre a müveletre?
     * @param string $action
     * @param string $parentType
     * @param int $parentId
     * @param int $userId
     * @return bool
     */
    protected function accessCheck(string $action, string $parentType, 
        int $parentId, int $userId): bool {
       $result = false;
       if (\Auth::check()) {
           if ($action == 'add') {
               $user = \Auth::user();
               if ($userId > 0) {
                   $result = ($user->id == $userId);
               } else {
                   $result = $this->model->userMember($parentType, $parentId, $userId);
               }
           }
           if ($action == 'edit') {
               $user = \Auth::user();
               if ($userId > 0) {
                   $result = ($user->id == $userId);
               } else {
                   $result = $this->model->userAdmin($parentType, $parentId, $userId);
               }
           }
           if ($action == 'delete') {
               $user = \Auth::user();
               if ($userId > 0) {
                   $result = ($user->id == $userId);
               } else {
                   $result = $this->model->userAdmin($parentType, $parentId, $userId);
               }
           }
       }
       return $result;
    }
    
    /**
     * Új felvitel képernyő
     * @param string $parentType
     * @param int $parentId
     * @param int $userId
     */
    public function create(string $parentType, int $parentId, int $userId) {
        if ($this->accessCheck('add',$parentType, $parentId, $userId)) {
            $fileRec = $this->model->emptyRecord();
            if ($userId > 0) {
                $fileRec->parent_type = "users";
                $fileRec->parent = $userId;
                $parentType = 'users';
                $parentId = $userId;
                $parent = \DB::table('users')->where('id','=',$userId)->first();
            } else {
                $fileRec->parent_type = $parentType;
                $fileRec->parent = $parentId;
                $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
            }
            if ($parent) {
                $fileRec->created_by = \Auth::user()->id;
                $result = view('file.form',[
                    "fileRec" => $fileRec,
                    "parentType" => $parentType,
                    "parentId" => $parentId,
                    "userId" => $userId,
                    "parent" => $parent
                ]);
            } else {
                $result = redirect()->to(\URL::previous())->with('error','fatal error parent not found');
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error',__('file.accessDenied'));
        }
        return $result;
    }
    
    /** új felvitel tárolása
     * 
     * @param Request $request
     */
    public function store(Request $request) {
        $errorInfo = '';
        $parentType = $request->input('parent_type');
        $parentId = $request->input('parent');
        if ($parentType == 'users') {
            $userId = $parentId;
        } else {
            $userId = 0;
        }
        if ($this->accessCheck('add',$parentType, $parentId, $userId)) {
            if ($this->model->valid($request)) {
                $id = 0;
                
                // rekord tárolása
                $errorInfo = $this->model->storeOrUpdate($id, $request);
                
                // uploaded file tárolása, rekord irása a files táblába
                if (!defined('UNITTEST')) {
                    $targetDir = 'storage/'.$parentType.'/'.substr((1000+$id),0,3).'/';
                    $targetName = substr((1000+$id),3,100);
                    if ($errorInfo == '') {
                        $errorInfo = Upload::processUpload('upload',
                        $targetDir,
                        $targetName,
                        []);
                        if (substr($errorInfo,0,5) == 'ERROR') {
                            $this->model->where('id','=',$id)
                            ->delete();
                        } else if ($errorInfo == 'no upload') {
                            $this->model->where('id','=',$id)
                            ->delete();
                            $errorInfo = __('file.notUploadFile');
                        } else {
                            // type modositása a rekordban
                            $i = strpos($errorInfo,'.');
                            if ($i > 0) {
                                $type = substr($errorInfo,($i+1),100);
                            } else {
                                $type = 'file';
                            }
                            $errorInfo = '';
                            $this->model->where('id','=',$id)
                            ->update(["type" => $type]);
                        }
                    }
                }
                
                if ($errorInfo == '') {
                        $parentType = $request->input('parent_type');
                        $parentId = $request->input('parent');
                        if ($parentType == 'users') {
                            $url = '/file/list/users/0/'.$parentId;
                        } else {
                            $url = '/file/list/'.$parentType.'/'.$parentId.'/0';
                        }
                        $result = redirect()->to(\URL::to($url))
                            ->with('success',__('file.saved'));
                    } else {
                        $result = redirect()->to(\URL::previous())->with('error',$errorInfo);
                    }
                }
        } else {
            $result = redirect()->to(\URL::previous())->with('error',__('file.accessDenied'));
        }
        return $result;
    }

    /**
     * Edit képernyő
     * @param int $fileId
     */
    public function edit(int $fileId) {
        $fileRec = $this->model->where('id','=',$fileId)->first();
        if ($fileRec) {
            $parentType = $fileRec->parent_type;
            $parentId = $fileRec->parent;
            if ($parentType == 'users') {
                $userId = $parentInd;
            } else {
                $userId = 0;
            }
            if ($this->accessCheck('edit',$parentType, $parentId, $userId)) {
                if ($userId > 0) {
                    $parent = \DB::table('users')->where('id','=',$userId)->first();
                } else {
                    $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
                }
                if ($parent) {
                    $result = view('file.form',[
                        "fileRec" => $fileRec,
                        "parentType" => $parentType,
                        "parentId" => $parentId,
                        "userId" => $userId,
                        "parent" => $parent
                    ]);
                } else {
                    $result = redirect()->to(\URL::previous())->with('error','fatal error parent not found');
                }
            } else {
                $result = redirect()->to(\URL::previous())->with('error',__('file.accessDenied'));
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error','fatal error file record not found');
        }
        return $result;
    }
    

    /** módosítás tárolása
     *
     * @param Request $request
     */
    public function update(Request $request) {
        $errorInfo = '';
        $parentType = $request->input('parent_type');
        $parentId = $request->input('parent');
        if ($parentType == 'users') {
            $userId = $parentId;
        } else {
            $userId = 0;
        }
        $id = $request->input('id');
        if ($this->accessCheck('edit',$parentType, $parentId, $userId)) {
            if ($this->model->valid($request)) {
                // rekord tárolása
                $errorInfo = $this->model->storeOrUpdate($id, $request);
                
                // uploaded file tárolása, rekord irása a files táblába
                if ((!defined('UNITTEST')) & ($_FILES['upload']['name'] != '')) {
                    $targetDir = 'storage/'.$parentType.'/'.substr((1000+$id),0,3).'/';
                    $targetName = substr((1000+$id),3,100);
                    if ($errorInfo == '') {
                        $errorInfo = Upload::processUpload('upload',
                            $targetDir,
                            $targetName,
                            []);
                        if (substr($errorInfo,0,5) == 'ERROR') {
                            $errorInfo = __($errorInfo);
                        } else if ($errorInfo == 'no upload') {
                            $errorInfo = __('file.notUploadFile');
                        } else {
                            // type modositása a rekordban
                            $i = strpos($errorInfo,'.');
                            if ($i > 0) {
                                $type = substr($errorInfo,($i+1),100);
                            } else {
                                $type = 'file';
                            }
                            $errorInfo = '';
                            $this->model->where('id','=',$id)
                            ->update(["type" => $type]);
                        }
                    }
                }
                
                if ($errorInfo == '') {
                    $parentType = $request->input('parent_type');
                    $parentId = $request->input('parent');
                    if ($parentType == 'users') {
                        $url = '/file/list/users/0/'.$parentId;
                    } else {
                        $url = '/file/list/'.$parentType.'/'.$parentId.'/0';
                    }
                    $result = redirect()->to(\URL::to($url))
                    ->with('success',__('file.saved'));
                } else {
                    $result = redirect()->to(\URL::previous())->with('error',$errorInfo);
                }
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error',__('file.accessDenied'));
        }
        return $result;
    }
    
    
    /**
     * file adatlap képernyő
     * @param int $id
     */
    public function show(int $id) {
        $file = $this->model->where('id','=',$id)->first();
        if ($file) {
            $parent = \DB::table($file->parent_type)
            ->where('id','=',$file->parent)
            ->first();
            if ($parent) {
                $info = $this->model->getInfo($file);
                $result = view('file.show',[
                    "file" => $file, 
                    "parent" => $parent,
                    "info" => $info
                ]);
            } else {
                $result = redirect()->to(\URL::previous())->with('error','fatal error parent not found');
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error','fatal error file record not found');
        }
        return $result;
    }
    
    public function download(int $id) {
        $fileRec = $this->model->where('id','=',$id)->first();
        if (!$fileRec) {
            $result = redirect()->to(\URL::previous())->with('error','fatal error file record not found');
        }
        if ($fileRec->parent_type == 'users') {
            $userId = $fileRec->parnt;
        } else {
            $userId = 0;
        }
        if (\Auth::check()) {
            if (($this->model->userMember($fileRec->parent_type, $fileRec->parent, $userId)) |
                ($fileRec->created_by == \Auth::user()->id)) {
                    $memberModel = new \App\Models\Member();
                    if ($memberModel->where('parent_type','=','files')
                        ->where('parent','=',$id)
                        ->where('user_id','=',\Auth::user()->id)
                        ->count() <= 0) {
                        $memberModel->create([
                            "parent_type" => "files",
                            "parent" => $id, 
                            "user_id" => \Auth::user()->id, 
                            "rank" => "downloader",
                            "status" => "active",
                            "created_by" => \Auth::user()->id
                        ]);
                    }
                    $filePath = 'storage/'.
                        $fileRec->parent_type.'/'.
                        substr(1000+$id,0,3).'/'.
                        substr(1000+$id,3,100).'.'.$fileRec->type;
                    if(file_exists($filePath)) {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="'.basename($filePath).'"');
                        header('Expires: 0');
                        header('Cache-Control: must-revalidate');
                        header('Pragma: public');
                        header('Content-Length: ' . filesize($filePath));
                        flush(); // Flush system output buffer
                        readfile($filePath);
                        die();
                    } else {
                        http_response_code(404);
                        die();
                    }
            } else {
                echo __('file.accessDenied'); exit();
            }
        } else {
            echo __('file.accessDenied'); exit();
        }
        return $result;
    }
}
