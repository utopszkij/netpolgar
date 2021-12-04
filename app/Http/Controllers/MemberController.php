<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;

class MemberController extends Controller
{
	 /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index(string $parent_type, string $parent = '0')
    {
		 // get $parent record (input param  $parent az ID)
       $parentId = $parent;
       $t = \DB::table($parent_type);
       $parent = $t->where('id','=',$parentId)->first();
		 if (!$parent) {
				echo 'Fatal error parent not fouund '.$parent_type.'/',$parentId; exit();		 
		 }


   	  $model = new Member();	
        $data = $model->select(['members.id',
        								  'members.user_id',
										  'members.rank',
										  'members.status',
										  'users.name',
										  'users.email',
										  'users.profile_photo_path'])
        ->leftJoin('users','users.id','=','members.user_id')
        ->where('members.parent_type','=',$parent_type)
        ->where('members.parent','=',$parent->id)
        ->orderBy('rank', 'asc')
        ->orderBy('name', 'asc')
        ->paginate(5);

        $info = $model->getInfo($parent_type, $parent, $data);
        		 
        return view('member.index',
            ["data" => $data,
                "parent_type" => $parent_type,
                "parent" => $parent,
                "info" => $info
            ])
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    
	/**
    * adatlap (almenü-> groups, projekts, products, files)
    * @param Member $member
    * @return \Illuminate\Http\Response
    */
    public function show(string $memberId) {
        $t = \DB::table('members');
        $member = $t->where('id','=',$memberId)->first();
        if (!$member) {
            echo 'Fatal error member not found'; exit();
        }
        
        $t = \DB::table($member->parent_type);
        $parent = $t->where('id','=',$member->parent)->first();
        if ($parent) {
            $t = \DB::table('users');
            $user = $t->where('id','=',$member->user_id)->first();
            
            $ranks = [];
            $t = \DB::table('members');
            $members = $t->where('parent_type','=',$member->parent_type)
                        ->where('parent','=',$member->parent)
                        ->where('user_id','=',$member->user_id)
                        ->get();
            foreach ($members as $m) {
                $ranks[] = __('member.'.$m->status.'_'.$m->rank);                
            }
            return view('member.show',
                ["member" => $member,
                 "parent" => $parent,
                 "ranks" => $ranks,   
                 "user" => $user
                ]);
        } else {
            echo 'Fatal error parent not found'; exit();
        }
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
    	if (($parent_type == '') | 
    	    ($parentId < 1) |
    	    ($rank == '')) {
			echo 'Fatal error'; exit();    	
    	}
    	// csak bejelentkezett usernél megengedett
    	if (\Auth::user()) {
    		$m = \DB::table('members');
    		// csak active parent -be megengedett
			$t = \DB::table($parent_type);
			$parent = $t->where('id','=',$parentId)->first();  		
			if (!$parent) {
					echo 'Fatal error parent not fouund '.$parent_type.'/',$parentId; exit();		 
			}
			if ($parent->status == 'active') {
	    		// csak ha még nincs ilyen "rank" -al rekord
			    $w = $m->where('parent_type','=',$parent_type)
	    				->where('parent','=',$parent->id)
	    				->where('rank','=',$rank)
	    				->where('user_id','=',$user->id)
	    				->first();
				if (!$w) {	    				
		    		$modelArr = [];
		    		$modelArr['parent_type'] = $parent_type;
		    		$modelArr['parent'] = $parent->id;
		    		$modelArr['rank'] = $rank;
		    		$modelArr['user_id'] = $user->id;
		    		$modelArr['created_by'] = $user->id;
		    		$modelArr['status'] = 'proposal';
		    		$model = new Member();
		    		$model->create($modelArr);
		    		$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
	    		} else {
		    		$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.exists'));
	    		}
    		} else {
	    		$result = redirect(\URL::to('/'.$parent_type.
	    		'/'.$parent->id))->with('error',__('member.notActive'));
    		}
    	} else {
	    	$result =redirect(\URL::to('/'.$parent_type.
	    		'/'.$parent->id))->with('error',__('member.notLogged'));
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
		$t = \DB::table($parent_type);
		$parent = $t->where('id','=',$parentId)->first();
		
	   if (!$parent) {
				echo 'Fatal error parent not fouund '.$parent_type.'/',$parentId; exit();		 
		}
		if ($parent->status == 'active') {
			// van másik admin? 
		    $m = \DB::table('members');
		    $otherAdmin = $m->where('parent_type','=',$parent_type)
		    ->where('parent','=',$parent->id)
		    ->where('rank','=','admin')
		    ->where('status','=','active')
		    ->where('user_id','<>',$user->id)->first();
			if ($rank == 'member') {
				if ($otherAdmin) {
				    $m = \DB::table('members');
				    $m->where('parent_type','=',$parent_type)
	    			->where('parent','=',$parent->id)
	    			->where('user_id','=',$user->id)->delete();
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
				} else {
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.youAreAdmin'));
				}
			} else if ($rank == 'admin') {
				if ($otherAdmin) {
				    $m = \DB::table('members');
    				$m->where('parent_type','=',$parent_type)
	    			->where('parent','=',$parent->id)
	    			->where('rank','=',$rank)
	    			->where('user_id','=',$user->id)->delete();
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id));
				} else {
					$result = redirect(\URL::to('/'.$parent_type.
		    		'/'.$parent->id))->with('error',__('member.youAreAdmin'));
				}
			} else {
			    $m = \DB::table('members');
  				$m->where('parent_type','=',$parent_type)
    			->where('parent','=',$parent->id)
    			->where('rank','=',$rank)
    			->where('user_id','=',$user->id)->delete();
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
