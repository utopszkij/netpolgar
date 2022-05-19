<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;

/**
 * myCron rendszer
 * 
 * ha a szerveren nincs mód crontab futtatására:
 * minden program aktivizálás behivja AJAX hivással URL: /cron
 * ez az exec methodot hajtja végre
 * 
 * ha van mód akkor a cron percenként ütemezve hivja "wget domain://cron"
 * 
 * crons table:
 *     id  (autoinc primary key)
 *     schelude 'minutes' | 'hours' | 'days'
 *     controller
 *     method
 *     data json string
 *     lastrun  bigint   (erre is van index) 
 * 
 * amikor végrehajt egy controller->method -t, annak a feladata, hogy a futását előidéző
 * table row -t 
 * - szükség esetén modositsa a következő aktivizáláshoz (kivéve a lastrun mezőt)
 * - vagy törölje ha nem kell újta futtatni
 * 
 * a meghívott methodosk két paramétert kapnak: $data, $id
 */
class MycronController extends Controller {
    /**
     * lekéri a rendszer időt, 
     * ha már több mint egy perce nem futott akkor:
     * végig nézi a táblát és amit kell azt futtatja, amit futtat annál modositja a 
     * lastrun -t a táblában
     */
    public static function exec() {
        $rec = \DB::table('crons')->orderBy('lastrun','desc')->first();
        if ($rec) {
            $lastrun = $rec->lastrun;
        } else {
            $lastrun = 0;
        }
        $time = time();
        if ($time >= ($lastrun + 60)) {
            $recs = \DB::table('crons')->get();
            if (count($recs) > 0) {
                foreach ($recs as $rec) {
                    if ((($rec->schelude == 'minutes') & ($time >= ($rec->lastrun + 60))) |
                        (($rec->schelude == 'hours') & ($time >= ($rec->lastrun + (60*60)))) | 
                        (($rec->schelude == 'days') & ($time >= ($rec->lastrun + (24*60*60))))) {
                            $this->execute($rec,$time);
                    }
                }    
            }
        }

    }

    /**
     * végrehjatja a $rec -ben lévő feladatot, módosítja a $rec -et a táblában
     * @param cron_record $rec
     * @param int $time
     */
    protected function execute($rec, int $time) {
        \DB::table('crons')->where('id','=',$rec->id)->update(['lastrun' => $time]);
        MycronController::writeLog(date('Y-m-d H:i:s').' '.$rec->controller.'->'.$rec->method);
        $controllerName = "\\App\\Http\\Controllers\\".$rec->controller;
        $methodName =  $rec->method;
        try {
            $controller = new $controllerName ();
            $controller->$methodName (JSON_decode($rec->data), $rec->id);
        } catch (Exception $e) {
            MycronController::writeLog('ERROR '.JSON_encode($e));
        }    
    }

    /**
     * Új feladataot ütemez
     * @param string $controller
     * @param string $method
     * @param string $schelude
     * @param object $data
     */
    public static function add(string $controller, string $method, string $schedule, $data) {
        \DB::table('crons')->insert([
            'controller' => $controller,
            'method' => $method,
            'schedule' => $schedule,
            'data' => JSON_encode($data)
        ]);
    }   

    /**
     * meglévő feladatot modosít
     * @param int $id
     * @param string $controller
     * @param string $method
     * @param string $schelude
     * @param object $data
     */
    public static function update(int $id, string $controller, string $method, string $schedule, $data) {
        \DB::table('crons')->where('id','=',$id)
            ->update([
            'controller' => $controller,
            'method' => $method,
            'schedule' => $schedule,
            'data' => JSON_encode($data)
        ]);
    }   

    /**
     * meglévő feladatot töröl
     * @param int $id
     */
    public static function remove($id) {
        \DB::table('cron')->where('id','=',$id)->delete();
    }

    /**
     * ir a conlog -ba
     * @param string $txt
     */
    public static function writeLog($txt) {
        if (file_exists('./storage/cronlog.txt')) {
            $fp = fopen('./storage/cronlog.txt','a+');
        } else {
            $fp = fopen('./storage/cronlog.txt','w+');
        }
        fwrite($fp, $txt);
        fclose($fp);    
    }
     
}
