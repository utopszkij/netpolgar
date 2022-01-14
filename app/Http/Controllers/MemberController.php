<?php
/**
* tagok kontroller
* publikus funkciók:
*   index($parent_type, $parent)
*   show($memberId)
*   user($userId)
*   store($request)
*   doExit($request)
*/ 

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller {
	
	 protected $model = false;
	 
	 function __construct() {
		$this->model = new Member();	 
	 }	

	 /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(string $parent_type, string $parent = '0')
    {
		 // get $parent record (input param  $parent az ID)
       $parentId = $parent;
       $parent = Member::getParent($parent_type, $parent);
		 $data = $this->model->getData($parent_type, $parentId, 8);
       $info = $this->model->getInfo($parent_type, $parent, $data);
		 foreach ($data as $d1) {
			$this->model->checkStatus($d1->id);		 
		 }	        		
        		 
        return view('member.index',
            ["data" => $data,
                "parent_type" => $parent_type,
                "parent" => $parent,
                "info" => $info
            ])
            ->with('i', (request()->input('page', 1) - 1) * 8);
    }
    
	/**
    * adatlap (almenü-> groups, projekts, products, files)
    * @param Member $member
    * @return \Illuminate\Http\Response
    */
    public function show(string $memberId) {
        $member = $this->model->where('id','=',$memberId)->first();
        if (!$member) {
            echo 'Fatal error member not found (show)'; exit();
        }
        $parent = Member::getParent($member->parent_type, $member->parent);
        $t = \DB::table('users');
        $user = $t->where('id','=',$member->user_id)->first();
		  $ranks = $this->model->getRanks($member);
        return view('member.show',
                ["member" => $member,
                 "parent" => $parent,
                 "ranks" => $ranks,   
                 "user" => $user
                ]);
    }
    
    /** user adatlap megjelenítése
     * 
     * @param string $userId
     * @return laravel view
     */
    public function user(string $userId) {
        $member = $this->model->where('user_id','=',$userId)
                    ->where('parent_type', '=', 'teams')
                    ->where('parent','=','1')
                    ->first();
        if (!$member) {
            echo 'Fatal error member not found(1)'; exit();
        }
        return $this->show($member->id);
    }
   
	 /**
	 * új rekord tárolása (jelentkezés tagnak vagy tisztségviselőnek)
	 * staus="proposal" user_id = \Auth::user()->id 
	 * @param Request $request (parent_type, parent, rank)
	 * @return  \Illuminate\Http\Response
	 */
   
    public function store(Request $request) {
    	$user = \Auth::user();
    	$parent_type = $request->input('parent_type');
    	$parentId = $request->input('parent');
    	$rank = $request->input('rank');
        $parent = Member::getParent($parent_type, $parentId);

    	if (($parent_type == '') | 
    	    ($parentId < 1) |
    	    ($rank == '')) {
			echo 'Fatal error'; exit();    	
    	}
    	
    	// csak bejelentkezett usernél megengedett
    	if (\Auth::user()) {
    		// csak active parent -be megengedett
			if ($parent->status == 'active') {
	    		// csak ha még nincs ilyen  rekord
				if (!Member::getRecord($parent_type, $parent->id, $rank, $user->id)) {	    				
					$errorInfo = $this->model->createRecord($parent_type, 
						$parent->id, $rank, $user					
					);
		 			if ($errorInfo != '') {
			    		$result = redirect(\URL::to('/'.$parent_type.
			    		'/'.$parent->id))->with('error',$errorInfo);
		 			} else {   		
			    		$result = redirect(\URL::to('/'.$parent_type.
			    		'/'.$parent->id));
		    	   }
	    		} else {
		    		$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.exists'));
	    		}
    		} else {
	    		$result = redirect(\URL::to('/'.$parent_type.
	    		'/'.$parent->id))->with('error',__('member.notActive'));
    		}
    	} else {
	    	$result = redirect(\URL::to('/'))->with('error',__('member.notLogged'));
    	}	
    	return $result;
    }
    
	 /**
	 * kilépés a csoportból vagy tisztség visszavonása
	 * (bejelentkezett user lép ki, mond le)
	 * @param Request $request (parent_type, parent, rank)
	 * @return  \Illuminate\Http\Response
	 */
    public function doExit(Request $request) {
    	$user = \Auth::user();
    	$parent_type = $request->input('parent_type');
    	$parentId = $request->input('parent');
    	$rank = $request->input('rank');
    	if (($parent_type == '') | 
    	    ($parentId < 1) |
    	    ($rank == '') |
    	    ($user == false)) {
			echo 'Fatal error'; exit();    	
    	}
      $parent = Member::getParent($parent_type, $parent);
		if ($parent->status == 'active') {
			// van másik admin? 
		    $otherAdmin = Member::getOtherAdmin($parent_type, $parent->id,
				$user->id);
			if ($rank == 'member') {
				if ($otherAdmin) {
					Member::deleteRecords($parent_type, $parentId,
						$userId, 'all');
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
				} else {
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.youAreAdmin'));
				}
			} else if ($rank == 'admin') {
				if ($otherAdmin) {
					Member::deleteRecords($parent_type, $parentId,
						$userId, 'admin');
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
				} else {
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.youAreAdmin'));
				}
			} else {
				Member::deleteRecords($parent_type, $parentId,
						$userId, $rank);
				$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
			}
		}	else {
			$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.notActive'));
		}
		return $result;	
    }
   
}
