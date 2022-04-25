<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelpController extends Controller {

    /**
     * help megjelenités (új böngésző fülön)
     * URL param name - help logikai neve
     */
    public function show(Request $request) {
        return view('help.show',["name" => $request->input('name','home')]);
    }
}
