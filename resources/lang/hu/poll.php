<?php
return [
  	'teams' => 'Csoport',
    'projects' => 'Projekt',
    'products' => 'Termék',

    'debates' => 'Viták',
    'polls' => 'Szavazások',

    'status' => 'Státusz',
    'debate' => 'Vita',
    'active' => 'Aktív',
    'closed' => 'Lezárt szavazás',
    'proposal' => 'Javasolt vita',

    'debateHelp' => 'Támogasd a leírás alatt lévő <em class="fas fa-thumbs-up"></em> -al a vita meginditását!',
    'activeHelp' => 'Aktív',
    'closedHelp' => 'Lezárult. Már nem lehet szavazni, kommentelni',
    'voteHelp' => 'Most folyik a szavazás',
    'proposalHelp' => 'Javasolj új választható opciókat, támogasd <em class="fas fa-thumbs-up"></em> -al mások ilyen javaslatait!',

    'proposalOption' => 'Javasolt opció',
    'canceled' => 'Megszakított vita',
    'vote' => 'Szavazás',
    
  	'config' => 'Beállítások',
    'pollType' => 'Típus',
    'yesno' => 'Igen/Nem',
    'onex' => 'Egy választható',
    'morex' => 'Több választható',
    'pref' => 'Sorba rendező',
    'prtition' => 'Felosztó',
    'secret' => 'Titkos',
    'public' => 'Nyílt',
    'liquied' => 'Likvid',
    'yes' => 'Igen',
    'no' => 'Nem',
    '1' => 'Igen',
    '0' => 'Nem',
    'like' => 'Vita indítását támogatom',
    'liked' => 'Vita indítását támogatták',
    'optionLike' => 'Szavazólapra vételét támogatom',
    'optionLiked' => 'Szavazólapra vételét támogatták',
    
    
    'id' => 'Azonosító',
  	'name' => 'Megnevezés',
    'description' => 'Leírás', 
    'notrecord' => 'Nincs adat', 
    'indexHelp' => 'További részletekért kattints a megnevezésre',
    
    'back' => 'Vissza a listához',
    'comments' => 'Hozzászólások',
    'votes' => 'Leadott szavazatok',
    'check' => 'Saját szavazatom',
    'files' => 'Fájlok',
    'events' => 'Események',
    'edit' => 'Szerkesztés',
    'add' => 'Új vita indítását javaslom',
    'options' => 'Opciók',
    'addOption' => 'Új opciót javaslok',
    'details' => 'Adatlap',
    'save' => 'Tárolás',
    'cancel' => 'Mégsem',
    'saved' => 'Adat tárolva',
    'ok' => 'Rendben',
    'voteNow' => 'Most szavazok',
    'accessDenied' => 'Ez a művelet számodra nem engedélyezett. :(',
    'liquiedSecretError' => 'Likvid opció csak nyílt szavazásnál megengedett',
    'successSave' => 'Adatok sikeresen tárolva.',
    'result' => 'Eredmény',
    'subResult' => 'Részeredmény', 

    'help' => 'Döntési folyamat:
    <ol>
      <li><strong>1. Javaslat</strong>: Egy tag javasolt egy eldöntendő kérdést, 
        és hozzá megoldási alternatívákat. A többiek a "like" gombbal jelezhetik, 
        hogy támogatják a kérdés "napirendre tüzését". Ha a javaslat
        elérte az előírt támogatottságot akkor "vitává" alakul.</li>
      <li><strong>2. Vita</strong>: Minden tag javasolhat új megoldási alternatívákat,
        illteve a "like" gombbal támogathatja mások megoldási javaslatait.
        A vita a beállított vita időszak letelte után "szavazássá" alakul, a szavazó
        lépernyőre azok a megoldási javaslatok kerülnek fel amelyek megkapták
        a beállított támogatást.</li>
      <li><strong>3. Szavazás</strong>: a beállított szavazás tipusnak megfelelő, 
        a beállított szavazási dőtartamig tartó szavazás. Szavazás közben a 
        részeredmény,  lezárulása után a végeredmény megtekinthető. A "likvid szavazás"
        algoritmus szerinti kiértékelés a szavazás lezárásakor történik meg.</li>
    </ol>
    '
];
