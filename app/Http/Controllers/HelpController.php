<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller {

    /**
     * help megjelenités google drive -on lévő prezentációból (új böngésző fülön)
     * URL param name - help logikai neve
     */
    public function show(Request $request) {
        return view('help.show',["name" => $request->input('name','home'),
                                 "m" => $request->input('m','?')]);
    }

    /**
     * html help megjelenitő
     * URL string param name 
     *     string m 'p' | 'd'
     * 'views/help/name_{m}.blade.php' view -t jelenit meg a help.page view-be beágyazva
     */
    public function page(string $name, Request $request) {
        $p = $request->input('p',0);
        $m = $request->input('m','?');
        if ($m == '?') {
            echo '
            <script>
                var name = "'.$name.'";
                var p = "'.$p.'";
                var m = "d";
                if (window.innerWidth < 575) {
                    m = "p";
                }
                document.location = "/help/page/"+name+"?m="+m;
            </script>
            ';
            exit();
        } else {
            return view('help.page', ["name" => $name, "m" => $m]);
        }
    }
}
