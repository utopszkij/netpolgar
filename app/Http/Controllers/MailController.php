<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Minimarkdown;
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
		$addressed = $request->session()->get('addressed','all');
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
						"addressed" => $addressed,
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
     * 1 db levél elküldése. Ha van még küldendő akkor vissza a formhoz
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
		$addressed = $request->input('addresed','');
		$subject = $request->input('subject','');
		$mailbody = $request->input('mailbody','');
		$mailbody = str_replace('&lt;','<',$mailbody);
		$mailbody = str_replace('&gt;','>',$mailbody);
		$request->session()->put('subject',$subject);
		$request->session()->put('mailbody',$mailbody);
		$request->session()->put('addressed',$addressed);
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
						->select('users.email','users.name')
						->leftJoin('users','users.id','members.user_id')
						->where('parent_type','=',$parentType)
						->where('parent','=',$parent)
						->where('rank','=','member')
						->where('status','=','active')
						->get();
					} else {	
						$w = explode(',',$addressed);
						$emails = [];
						foreach ($w as $w1) {
							$emails[] = JSON_decode('{"email":"'.trim($w1).'", "name":"'.$w1.'"}');
						}
					}

					if ($offset < count($emails)) {
						for ($i = $offset; $i < ($offset + 1); $i++) {
							if ($i < count($emails)) {
								// levél szöveg kialakítása
								$mailbody = str_replace('{name}',$emails[$i]->name, $mailbody);
								$mailbody = '<html>'.
								'<head><meta charset="utf-8"></head>'.
								'<body>'.Minimarkdown::miniMarkdown($mailbody).
								'</body></html>';	
			
								/* levélküldés $emails[$i] -nek */
								\Mail::to(['html' => $emails[$i]->email])
								->send(new \App\Mail\NewsletterMail($subject, $mailbody)); 
								if (!file_exists('./storage/maillog.txt')) {
									$fp = fopen('./storage/maillog.txt','w+');
								} else {	
									$fp = fopen('./storage/maillog.txt','a+');
								}
								fwrite($fp, \Auth::user()->name.' to= '.$emails[$i]->email. ' '.date('H:i:s')."\n");
								$j++;
								if (\Mail::failures()) {
							   		$errors .= 'mail send error. target:'.$emails[$i]->email.' ';
									fwrite($fp, '******** mail error'."\n");   
								}
								fclose($fp);
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


