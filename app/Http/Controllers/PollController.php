<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\Poll;
use \App\Models\Vote;


class PollController extends Controller {

	protected $model = false;

	function __construcT() {
		$this->model = new Poll();
	}	

    
    /**
     * Display a listing of the resource.
     * @param string $parentType
     * @param string $parent
     * @param string $statuses pl: proposal-debate
     * @return \Illuminate\Http\Response
     */
    public function index(string $parentType, string $parent, string $statuses) {
        $parent = Poll::getParent($parentType,$parent);
	    // adat lekérés, poll statuszok ellenörzése
		$data = $this->model->getData($parentType, $parent->id, $statuses, 8);
        return view('poll.index',
            ["data" => $data,
             "parentType" => $parentType,
             "parent" => $parent,
             "statuses" => $statuses,   
             'userMember' => Poll::userMember($parentType, $parent->id)
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
        $parent = Poll::getParent($parentType,$parent);
        $poll = Poll::emptyRecord();
        $poll->parent_type = $parentType;
        $poll->parent = $parent->id;
        
        if (!$this->accessCheck('add',$parentType, $parent->id)) { 
                return redirect()->to(\URL::to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses))
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request (parentType, parent, ...record mezők... statuses)
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)  {
        $parentType = $request->input('parent_type');
        $parentId = $request->input('parent');
        $statuses = $request->input('statuses');
        $parent = Poll::getParent($parentType,$parentId);
        if (!$parent) {
            echo 'fatal error parent not found'; exit();
        }
        
        // csak parent csoport tag vihet fel
        if (!$this->accessCheck('add',$parentType, $parent->id)) { 
                return redirect()->to(\URL::to('/'.$parentType.'/'.$parent->id.'/'.$statuses.'/polls'))
                ->with('error',__('poll.accessDenied'));
         }
        $this->model->valid($request);
        $id = 0;    
        $errorInfo = Poll::saveOrStore($id, $request);
        $pollType = $request->input('pollType');
        if (($errorInfo == '') & ($pollType == 'yesno')) {
			$this->model->addYesNoOptions($id);
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
     * Request -ben érkezhet l_ParentType, l_ParentId is
     * likvid feldolgozáshoz esetenként többször visszahivja sajátmagát
     * @param  \App\Models\Poll  $poll
     * @return \Illuminate\Http\Response
     */
    public function show(Poll $poll) {
        $model = new \App\Models\Poll();
        $poll->status = $model->checkStatus($poll->id);
        $this->decodeConfig($poll);
        $parent = Poll::getParent($poll->parent_type, $poll->parent);
        $info = Poll::getInfo($poll);
        $voteModel = new \App\Models\Vote();
        $voteInfo = $voteModel->getInfo($poll);
        
        $options = \DB::table('options')->where('poll_id','=',$poll->id)->get();
        return view('poll.show',
            ["poll" => $poll,
             "parent" => $parent,
             "info" => $info,   
             "options" => $options,
             "userMember" => Poll::userMember($poll->parent_type, $poll->parent),
             "userAdmin" => Poll::userAdmin($poll),
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
        $parent = Poll::getParent($parentType,$poll->parent);

        $statuses = $poll->status;
        $this->decodeConfig($poll);
        
        // csak a admin modosíthat proposal és debate státuszban
		if (!$this->accessCheck('edit',$parentType, $parent->id, $poll)) {
                return redirect()->to(\URL::to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses))
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
        $parent = Poll::getParent($parentType,$poll->parent);
        $statuses = $request->input('statuses');
        
        // csak admin modosíthat proposal és debate státuszban
		if (!$this->accessCheck('edit',$parentType, $parent->id, $poll)) {
             return redirect()->to(\URL::to('/poll/list/'.$parentType,'/'.$parent->id.'/'.$statuses))
             ->with('error',__('poll.accessDenied'));
        }
            
        // tartalmi ellenörzés
        $this->model->valid($request);
        
        // poll rekord kiirása
        $id = $poll->id;
        $errorInfo = Poll::saveOrStore($id, $request);
            
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

	/**
	 * Hozzáférés engedélyezett?
	 * @param string $action
	 * @param string éparentType
	 * @param int $parentId, 
	 * @param Poll $poll
	 * @return bool
	 */ 
    protected function accessCheck(string $action, string $parentType, 
		int $parentId, $poll = false): bool {    
		$result = false;
		if ($action == 'add') {	
			$result = ((\Auth::check()) &
					   (Poll::userMember($parentType, $parentId))); 
        }
		if ($action == 'edit') {
			$result =  ((\Auth::check()) &
						(Poll::userAdmin($poll)) &
						($poll->status != 'voks') &
						($poll->status != 'closed') &
						($poll->status != 'canceled'));
		}
        return $result;
    }        
    
}
