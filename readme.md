# NETPOLGAR web-es közösségi platform

## Készültség

1%

## Élő demo:

https://netpolgar.hu

## Áttekintés

Et a web-es program egy új tipusú társadalom szervezés kialakítását kivánja elősegíteni. Ebben a vizióban egy olyan társadalom képe lebeg a fejlesztők szeme előtt  ahol
- a gazdaság van az emberért (és nem forditva). A gazdaság célja az emberek jólétének hosszú távon fenntartható megteremtése,  
- az emberek a kölcsönösen elönyös együttmüködés érdekében kordinálják tevékenységüket, a vezérlő elv az egymás segítése, gazdagítása nem pedig a versengés,
- Mindezekhez szükség van a kölcsönös bizalom kialakítására. Bizni abban tudunk akit ismerünk, akiről tudjuk mit csinált eddigi életében, hogyan gondolkozik. Ennek érdekében a program a nagyon szorossan vett személyes magán szféra kivételével a teljes nyiltságra, átláthatóságra ösztönzi a résztvevőket,
- Mivel ejelenlegi pénzrendszer a tőkeé profit termelés logikáját szolgálja ki, alternatív elszámolási rendszer kialakítását tüztük ki cálul.
- A szoftver segitségével létrehozott szellemi termékek (beleéprtve az anyagi termékek konstrukciós és technológiai dokumentációit is) a közösség részére szabadon felhasználhatóak.


## Programnyelvek

PHP(7.1+), Javascript, MYSQL, JQUERY, bootstrap
 
A program MVC ajánlás szerint struktúrált.

## Licensz

GNU/GPL
 
## Programozó

Fogler Tibor (Utopszkij)

tibor.fogler@gmail.com 

https://github.com/utopszkij


### GDPR megfelelés

kidolgozás alatt áll....

### Tesztelés
```
cd repoRoot
./tools/test.sh
```

## Dokumentálás
```
cd repoRoot
./tools/documentor.sh
```
A dokumentáció a "doc" könyvtárba kerül

## kód minőség ellenörzés
```
cd repoRoot
./tools/sonar.sh
```
Utolsó teszt eredménye:


## Új funkció létrehozásához szükéges fájlok

- models/ujfunkcio.php
- views/ujfunkcio.php
- views/html/ufunkcio_formname.html
- js/ujfunkcio.js
- controllers/ujfunkcio.php ('tasknév' nevű publikus funciók)
- langs/ujfunkcio_hu.php

Inditó link(ek):  domain/opt/ujfuncio/tasknév


## Telepítés web szerverre

### Rendszer igény:

- PHP 7.1+  (curl, mysqli, mbstring)
- MYSQL 5.7+
- web server (.htaccess értelmezéssel, végrehajtási sorrend: php, html)
- https tanusitvány
- Létrehozandó MYSQL adatbázis: **netpolgar** (utf8, magyar rendezéssel)


Telepítendő  könyvtárak:
- controllers
- core
- images
- js
- langs
- log (legyen irható a web szerver számára!)
- models
- templates
- vendor
- views
- work (legyen irható a web szerver számára!)

Telepítendő fájlok a documentRoot -ba:
- index.php
- .config.php  (config.txt átnevezve és értelemszerüen javítva)
- .htaccess (a htaccess.txt átnevezve)


