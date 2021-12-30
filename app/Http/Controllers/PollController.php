<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Poll;
use \App\Models\Vote;
use \App\Rules\LiquedRule;


class PollController extends Controller {
    
    /**
     * Bejelntkezett user tag a parent -ben?
     * @param string $parentType
     * @param string $parent
     * @return bool
     */
    protected function userMember(string $parentType, int $parentId):bool {
        if (\Auth::user()) {
            $result = (\DB::table('members')
                ->where('parent_type','=',$parentType)
                ->where('parent','=',$parentId)
                ->where('user_id','=',\Auth::user()->id)
                ->where('status','=','active')
                ->count() > 0);
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Bejelntkezett user admin ebben a szavazásban?
     * @param Poll $poll
     * @return bool
     */
    protected function userAdmin($poll):bool {
        if (\Auth::user()) {
            $result = (\DB::table('members')
                ->where('parent_type','=',$poll->parent_type)
                ->where('parent','=',$poll->parent)
                ->where('user_id','=',\Auth::user()->id)
                ->where('rank','=','admin')
                ->where('status','=','active')
                ->count() > 0);
            if (\Auth::user()->id == $poll->created_by) {
            	$result = true;
            }    
        } else {
            $result = false;
        }
        return $result;
    }

    /**
     * Display a listing of the resource.
     * @param string $parentType
     * @param string $parent
     * @param string $statuses pl: proposal-debate
     * @return \Illuminate\Http\Response
     */
    public function index(string $parentType, string $parent, string $statuses) {
        $parent = \DB::table($parentType)->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'fatal error parent not found'; exit();
        }
        $model = new \App\Models\Poll();

		  // adat lekérés, poll statuszok ellenörzése
		  $updated = true;
		  $counter = 0;
		  while (($updated) & ($counter < 20)) {	
	        $data = $model->latest()
	        ->where('parent_type','=',$parentType)
	        ->where('parent','=',$parent->id)
	        ->whereIn('status', explode('-',$statuses))
	        ->orderBy('created_at')
	        ->paginate(8);
	        $updated = false;
	        foreach ($data as $item) {
	        	  $oldStatus = $item->status;	
	           $item->status = $model->checkStatus($item->id);
	           if ($item->status != $oldStatus) {
						$updated = true;           
	           }
	        }
	        $counter++;
        }
        
        return view('poll.index',
            ["data" => $data,
             "parentType" => $parentType,
             "parent" => $parent,
             "statuses" => $statuses,   
             'userMember' => $this->userMember($parentType, $parent->id)
            ])
            ->with('i', (request()->input('page', 1) - 1) * 8);
    }
    
    /**
     * Show the form for creating a new resource.
     * @param string $parentType
     * @param string $parent
     * @param string $statuses
     * @return \Illuminate\Http\Response
     */
    public function create(string $parentType, string $parent, string $statuses)  {
        $parent = \DB::table($parentType)->where('id','=',$parent)->first();
        if (!$parent) {
            echo 'fatal error parent not found'; exit();
        }
        
        $poll = Poll::emptyRecord();
        $poll->parent_type = $parentType;
        $poll->parent = $parent->id;
        
        // csak parent csoport tag vihet fel
        if ((!\Auth::user()) |
            (!$this->userMember($parentType, $parent->id))) {
                return redirect()->to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses)
                ->with('error',__('poll.accessDenied'));
         }
            
         return view('poll.form',
                ["parentType" => $parentType,
                 "parent" => $parent,
                 "poll" => $poll,
                 "statuses" => $statuses
                ]);
    }
    
    /**
     * poll rekord irása az adatbázisba a $request-be lévő információkból
     * @param int $id
     * @param Request $request
     * @return string, $id created new record id
     */
    protected Function saveOrStore(int &$id, Request $request): string {
        $parentType = $request->input('parent_type');
        $parent = $request->input('parent');
            
        // rekord array kialakitása
        $pollArr = [];
        $pollArr['parent_type'] = $request->input('parent_type');
        $pollArr['parent'] = $request->input('parent');
        $pollArr['name'] = strip_tags($request->input('name'));
        $pollArr['description'] = strip_tags($request->input('description'));
        if ($id == 0) {
            $pollArr['status'] = 'proposal';
            if (\Auth::user()) {
                $pollArr['created_by'] = \Auth::user()->id;
            } else {
                $pollArr['created_by'] = 0;
            }
        }
        
        // config kialakitása
        $config = new \stdClass();
        $config->pollType = $request->input('pollType');
        $config->secret = $request->input('secret');
        $config->liquied = $request->input('liquied');
        $config->debateStart = $request->input('debateStart');
        $config->optionActivate = $request->input('optionActivate');
        $config->debateDays = $request->input('debateDays');
        $config->voteDays = $request->input('voteDays');
        $config->valid = $request->input('valid');
        $pollArr['config'] = JSON_encode($config);
        
        // poll rekord tárolás az adatbázisba
        $errorInfo = '';
        try {
            $model = new Poll();
            if ($id == 0) {
                $pollRec = $model->create($pollArr);
                $id = $pollRec->id;
            } else {
                $model->where('id','=',$id)->update($pollArr);
            }
        } catch (\Illuminate\Database\QueryException $exception) {
            $errorInfo = $exception->errorInfo;
        }
        return $errorInfo;
    }
    
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request (parentType, parent, ...record mezők... statuses)
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)  {
        $parentType = $request->input('parent_type');
        $parentId = $request->input('parent');
        $statuses = $request->input('statuses');
        $parent = \DB::table($parentType)->where('id','=',$parentId)->first();
        if (!$parent) {
            echo 'fatal error parent not found'; exit();
        }
        
        // csak parent csoport tag vihet fel
        if ((!\Auth::user()) |
            (!$this->userMember($parentType, $parent->id))) {
                return redirect('/'.$parentType.'/'.$parent->id.'/'.$statuses.'/polls')
                ->with('error',__('poll.accessDenied'));
            }
        
        // tartalmi ellenörzés
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'liquied' => new LiquedRule(),
            'debateStart' => ['numeric','min:0','max:100'],
            'optionActivate' => ['numeric','min:0','max:100'],
            'debateDays' => ['numeric','min:0','max:500'],
            'voteDays' => ['numeric','min:0','max:500'],
            'valid' => ['numeric','min:0','max:100']
        ]);
        
        $id = 0;    
        $errorInfo = $this->saveOrStore($id, $request);
        
        $pollType = $request->input('pollType');
        if (($errorInfo == '') & ($pollType == 'yesno')) {
				$optionsTable = \DB::table('options');
				$optionsTable->insert([
					"poll_id" => $id, 
					"name" => "Igen", 
					"description" => "", 
					"status" => "active",
					"created_by" => \Auth::user()->id			
				]);        
				$optionsTable->insert([
					"poll_id" => $id, 
					"name" => "Nem", 
					"description" => "", 
					"status" => "active",
					"created_by" => \Auth::user()->id			
				]);        
				
        }
        
        // result kialakitása
        if ($errorInfo == '') {
            $result = redirect('/polls/'.$id)
            ->with('success',__('poll.successSave') );
        } else {
            $result = redirect('/'.$parentType.'/'.$parent->id.'/'.$statuses.'/polls')
            ->with('error',$erroInfo);
        }
        return $result;
    }
    
    /**
     * poll->config json string dekodolása
     * @param Poll $poll
     * @return void
     */
    
    protected function decodeConfig(Poll &$poll) {
        $poll->config = JSON_decode($poll->config);
        if (!isset($poll->config->optionActivate)) {
				$poll->config->optionActivate = 2;        
        }
    }
    
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll) {
        $model = new \App\Models\Poll();
        $poll->status = $model->checkStatus($poll->id);
        $this->decodeConfig($poll);
        $parent = \DB::table($poll->parent_type)->where('id','=',$poll->parent)->first();
        $info = Poll::getInfo($poll);
        $voteModel = new \App\Models\Vote();
        $voteInfo = $voteModel->getInfo($poll);
        
        $options = \DB::table('options')->where('poll_id','=',$poll->id)->get();
        return view('poll.show',
            ["poll" => $poll,
             "parent" => $parent,
             "info" => $info,   
             "options" => $options,
             "userMember" => $this->userMember($poll->parent_type, $poll->parent),
             "userAdmin" => $this->userAdmin($poll),
             "voteInfo" => $voteInfo,
        ]);
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function edit(Poll $poll)   {
        $parentType = $poll->parent_type;
        $parent = \DB::table($poll->parent_type)->where('id','=',$poll->parent)->first();
        $statuses = $poll->status;
        $this->decodeConfig($poll);
        
        // csak a admin modosíthat proposal és debate státuszban
        if ((!\Auth::user()) |
            (!$this->userAdmin($poll)) |
            ($poll->status == 'voks') |
            ($poll->status == 'closed') |
            ($poll->status == 'canceled')
            ) {
                return redirect()->to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses)
                ->with('error',__('poll.accessDenied'));
        }
            
        return view('poll.form',
            ["parentType" => $parentType,
             "parent" => $parent,
             "poll" => $poll,
             "statuses" => $statuses   
            ]);
       
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Poll $poll)   {
        $parentType = $poll->parent_type;
        $parent = \DB::table($poll->parent_type)->where('id','=',$poll->parent)->first();
        $statuses = $request->input('statuses');
        
        // csak admin modosíthat proposal és debate státuszban
        if ((!\Auth::user()) |
            (!$this->userAdmin($poll)) |
            ($poll->status == 'voks') |
            ($poll->status == 'closed') |
            ($poll->status == 'canceled')
            ) {
                return redirect()->to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses)
                ->with('error',__('poll.accessDenied'));
        }
            
        // tartalmi ellenörzés
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'liquied' => new LiquedRule(),
            'debateStart' => ['numeric','min:0','max:100'],
            'optionActivate' => ['numeric','min:0','max:100'],
            'debateDays' => ['numeric','min:0','max:500'],
            'voteDays' => ['numeric','min:0','max:500'],
            'valid' => ['numeric','min:0','max:100']
        ]);
        
        // poll rekord kiirása
        $id = $poll->id;
        $errorInfo = $this->saveOrStore($id, $request);
            
        // result kialakítása
        if ($errorInfo == '') {
                $result = redirect()->to('/polls/'.$poll->id)
                ->with('success',__('poll.successSave'));
        } else {
                $result = redirect()->to('/polls/'.$poll->id)
                ->with('error',$errorInfo);
        }
        return $result;
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function destroy(Poll $poll)  {
        // törlés itt nem megengedett
    }
    
}
