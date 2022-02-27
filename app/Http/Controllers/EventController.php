<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Member;

class EventController extends Controller {
    
    protected $model = false;
    
    function __construct() {
        $this->model = new Event();
    }
     /**
     * a bejelentkezett felhasználó a like ikonra kattintott
     * - ha korábban már lájkolta akkor törli azt a likes táblából
     * - ha korábban még nem lájkolta a akkor létrehozza a likes táblában
     * @param string $parent_type
     * @param string $parent
     * @return laravel redirect back
     */
    public function like(string $parent_type, string $parent) {
        $user = \Auth::user();
        if ($user) {
            $exists = Like::getRecord($parent_type, $parent, $user->id, 'like');
            if ($exists) {
				 Like::delRecord($parent_type, $parent, $user->id, 'like');
            } else {
                 Like::createRecord($parent_type, $parent, $user->id, 'like');
            }
        }
        return \Redirect::back();
    }
    
    /**
     * like/dislike user lisita megjelenítése
     * @param string $parentType
     * @param string $parent
     * @return laravel view
     */
    public function likeInfo(string $parentType, string $parent) {
        $likeUsers = Like::getList($parentType, $parent, 'like');
        $disLikeUsers = Like::getList($parentType, $parent, 'dislike');
        
		$parent = Like::getParent($parentType, $parent);
        return view('like.info',["likeUsers" => $likeUsers,
                                "disLikeUsers" => $disLikeUsers,
                                "parentType" => $parentType,
                                "parent" => $parent]);
    }
    
    /**
     * Lapozható lista megjelenitése
     * @param string $parentType
     * @param int $parentId
     */
    public function index(string $parentType, int $parentId) {
        $result = false;
        $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
        if ($parent) {
            $data = $this->model->getData($parentType, $parentId, 8);
            $userAdmin = Member::userAdmin($parentType, $parentId);
            $result = view('event.index',[
                "parent" => $parent, 
                "data" => $data, 
                "userAdmin" => $userAdmin,
                "parentType" => $parentType,
                "parentId" => $parentId
            ])->with('i', (request()->input('page', 1) - 1) * 8);;
        } else {
            $result = redirect()->to(\URL::previous())->with('error','parent not found');
        }
        return $result;
    }
    
    /**
     * Új felvitel képernyő megjelenitése
     * @param string $parentType
     * @param int $parentId
     */
    public function create(string $parentType, int $parentId) {
        $result = false;
        if (\Auth::check()) {
            $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
            if (!Member::userAdmin($parentType, $parentId)) {
                $result = redirect()->to(\URL::previous())->with('error',__('event.accessDenied'));
            } else if ($parent) {
                $event = $this->model->emptyRecord();
                $event->created_by = \Auth::user()->id;
                $event->parent_type = $parentType;
                $event->parent = $parentId;
                $userAdmin = Member::userAdmin($parentType, $parentId);
                $result = view('event.form',[
                    "parent" => $parent,
                    "event" => $event,
                    "userAdmin" => true,
                    "parentType" => $parentType,
                    "parentId" => $parentId
                ])->with('i', (request()->input('page', 1) - 1) * 8);;
            } else {
                $result = redirect()->to(\URL::previous())->with('error','parent not found');
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error',__('event.accessDenied'));
        }
        return $result;
    }

    /**
     * Új felvitel képernyő tárolás
     * @param Request $request
     */
    public function store(Request $request) {
        $result = false;
        $id = $request->input('id',0);
        $parentType = $request->input('parent_type');
        $parentId = $request->input('parent');
        if (\Auth::check()) {
            if (Member::userAdmin($parentType, $parentId)) {
                if ($this->model->valid($request)) {
                    $errorInfo = $this->model->updateOrCreate($request);
                    if ($errorInfo == '') {
                        $result = redirect()->to(\URL::to('/'.$parentType.'/'.$parentId.'/events'))
                        ->with('success',__('event.saved'));
                    } else {
                        $result = redirect()->to(\URL::previous())->with('error',$errorInfo);
                    }
                } else {
                    $result = redirect()->to(\URL::previous())->with('error',__('event.error'));
                }
            } else {
                $result = redirect()->to(\URL::previous())->with('error',__('event.accessDenied'));
            }
        } else {
            $result = redirect()->to(\URL::previous())->with('error',__('event.accessDenied'));
        }
        return $result;
    }
    
}
