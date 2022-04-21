<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MailController extends Controller
{
    /**
     * levél küldő form megjelenítése.
     * a küldendő levél szöveg és subjekt sessionból is jöhet
	 * URL GET -ben jöhet total is
	 * csak a $parentType adminisztrátorok használhatják
	 * @param string $parentType
	 * @param int $parent
	 * @param int $offset
	 * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function form(string $parentType, int $parent, int $offset, Request $request)   {
		$subject = $request->session()->get('subject','');
		$mailbody = $request->session()->get('mailbody','');
		$parentRec = \DB::table($parentType)->where('id','=',$parent)->first();
		$total = $request->input('total',0);
		if (\Auth::check()) {
			$admin = \DB::table('members')
			->where('parent_type','=',$parentType)
			->where('parent','=',$parent)
			->where('user_id','=',\Auth::user()->id)
			->where('rank','=','admin')
			->where('status','=','active')
			->first();
			if ($admin) {
				if ($parentRec) {
					return view('mail.form',[
						"parentRec" => $parentRec,
						"parentType" => $parentType,
						"parent" => $parent,
						"offset" => $offset,
						"subject" => $subject,
						"mailbody" => $mailbody,
						"total" => $total 
					]);
				} else {
					echo 'Fatal error parent not found'; exit();
				}
			} else {
				echo 'Fatal error not admin'; exit();
			}
		} else {
			echo 'Fatal error not logged'; exit();
		}
    }

	/**
     * 10 db levél elküldése. Ha van még küldendő akkor vissza a formhoz
	 * a küldendő levél szövegét és subjektjét sessionba tárolja
	 * csak a $parentType adminisztrátorok használhatják
	 * @param string $parentType
	 * @param int $parent
	 * @param int $offset
     * @return \Illuminate\Http\Response
	 * @param Request $request
     */
    public function send(string $parentType, int $parent, int $offset, Request $request)   {
		$result = '';
		$addressed = $request->input('addressed','');
		$subject = $request->input('subject','');
		$mailbody = $request->input('mailbody','');
		$mailbody = str_replace('&lt;','<',$mailbody);
		$mailbody = str_replace('&gt;','>',$mailbody);
		$request->session()->put('subject',$subject);
		$request->session()->put('mailbody','mailbody');
		$parentRec = \DB::table($parentType)->where('id','=',$parent)->first();
		if (\Auth::check()) {
			$admin = \DB::table('members')
			->where('parent_type','=',$parentType)
			->where('parent','=',$parent)
			->where('user_id','=',\Auth::user()->id)
			->where('rank','=','admin')
			->where('status','=','active')
			->first();
			if ($admin) {
				if ($parentRec) {
					$errors = '';
					$j = 0;
					if ($addressed == 'all') {
						$emails = \DB::table('members')
						->select('users.email')
						->leftJoin('users','users.id','members.user_id')
						->where('parent_type','=',$parentType)
						->where('parent','=',$parent)
						->where('rank','=','member')
						->where('status','=','active')
						->get();
					} else {	
						$emails = explode(',',$addressed);
					}
					if ($offset < count($emails)) {
						for ($i = $offset; $i < ($offset + 10); $i++) {
							if ($i < count($emails)) {
								// levélküldés $emails[$i] -nek
								\Mail::to($emails[$i])
								->send(new \App\Mail\NewsletterMail($subject, $mailbody));						
								$j++;
								if (\Mail::failures()) {
							   		$errors .= 'mail send error. target:'.$emails[$i].' ';
								}
								
								echo 'mail send to: '.$emails[$i].' '.$subject.' '.$mailbody.' '.$errors; exit();
							}	
						}	
						$offset = $offset + $j;
						if ($errors != '') {
							$result = redirect()->to(\URL::to('/mails/form/'.$parentType.'/'.$parent.'/'.$offset.'?total='.count($emails)))
							->width('error',$errors);
						} else if ($offset < count($emails)) {
							$result = redirect()->to(\URL::to('/mails/form/'.$parentType.'/'.$parent.'/'.$offset.'?total='.count($emails)));
						} else {
							$result = redirect()->to(\URL::to('/'))->with('success','emails sends');
						}	
					} else {
						if (count($emails) == 0) {
							$result = redirect()->to(\URL::to('/'))->with('error','not members');
						} else {
							$result = redirect()->to(\URL::to('/'))->with('success','emails sends');
						}
					}
				} else {
					echo 'Fatal error parent not found'; exit();
				}
			} else {
				echo 'Fatal error not admin'; exit();
			}
		} else {
			echo 'Fatal error not logged'; exit();
		}
		return $result;
    }
}


