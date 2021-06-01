# Netpolgar 

státusz: fejlesztés alatt

Készültség: 1%  v0.04-alpha

Verzió történet:
   v0.04 2021.05.   Laravel változat


[Élő demo](https://netpolgar.hu)

![Logo](public/img/logo.png)

## Áttekintés

A Netpolgár egy virtuális közösségi tér vagyis E-demokrácia felület.
Ez egy pártoktól, szervezetektől független civil kezdeményezés.
Célja egy alternatív hierarchia mentes, egyenrangú autonóm egyedekből álló együttműködési rendszer kimunkálása, gyakorlati kipróbálása.

### Fő jellemzők

Minden felhasználónak van egy üzenőfala, fájl,kép és videó tároló területe, esemény naptára, A felhasználók "ismerősnek" jelölhetik egymást (az ismeretséget a másik félnek is el kell fogadnia), A felhasználók csoportokat hozhatnak létre, csatlakozhatnak mások által létrehozott csoportokhoz (a csatlakozás rendjét a csoport szabályzata határozza meg). A csoportoknak is van üzenő faluk, file, kép és videó tárolójuk. Ezek a csoport tagjai számára elérhetőek, A csoportok projekteket (közösen megvalósított munkafolyamatokat, célkitűzéseket) indíthatnak. A projekteknek is van üzenőfaluk, file, fotó és videó területük. Ezek a projekt-gazda csoport tagjai számára elérhetőek, A felhasználó egy "összefésült" üzenőfalon láthatja a saját postjait, ismerősei postjait, azon csoportok üzenőfalát amelyeknek tagja, és ezen csoportok projektjeinek az üzenőfalát, A felhasználók privát üzeneteket is küldhetnek egymásnak, üzenet küldhet egy csoport összes tagjának, összes ismerősnek is, Szellemi és anyagi termékek cseréjét, megosztását virtuális, alternatív fizetőeszközös web-es piactér segít, A felhasználók NET -es szavazásokat indíthatnak, ezek rendelhetőek csoporthoz, projekthez, kommentelhetnek, értékelhetnek, szavazhatnak, A rendszerhez bárki önként csatlakozhat, ingyenesen használhatja azt.

## technikai infok

Sotware: Laravel  8.28.1 alapon készül. lásd: [laravel-readme.md](laravel-readme.md) és [laravel.com](http://laravel.com)

További felhasznált szellemi termékek: [jQuery](http://jquery.com), [bootstrap](https://getbootstrap.com/), [Awesore fonts](https://fontawesome.com/),
[pixabay](https://pixabay.com/),  [gravatar](http://gravatar.com), [facebook](http://facebook.com), [google](http://google.com), [github](http://github.com),
[spatie cookie consent](https://github.com/spatie/laravel-cookie-consent), 
[jitsin vide meating](https://jitsi.org/) 

## Licensz

[MIT license](https://opensource.org/licenses/MIT).

## A repo clonozása utáni teendők

composer create

npm install

mysql adatbázis létrehozása utf8mb4-hungaian_ci default rendezéssel

.env file editásása (mysql elérés, smtp elérés, opcionálisan github, facebook, google login konfig)

php artisan migrate

## lokális teszt futtatás
```
php artisan serve
```
## tests
```
php artisan test
```
## Feltöltés WEB szerverre

1. MYSQL adatbázis létrehozása (utf8m4_hunagrain_ci illesztéssel) és kezdeti feltöltése (parancssori mysql vagy phpmyadmin -al)

2. .env módosítása az aktuális adatbázis elérés ,levelezési és web site url beállításokhoz.

3. A továbbiak attól függően másként alakulnak, hogy van-e lehetőségünk a web szerver document_root modosítására.

### 3.1 ha van lehetőségünk a szerveren a document_rot modositására:
 
könyvtár struktúra a web szerveren:

```
    app/                 
    bootstrap/           
    config/
    database/
    public/         <- Ide mutasson a web szerver document_root!
                       kell bele egy symlink a storage könyvtárra.
    resources/
    routes
    storage/
    vendor/
```

fájlok a fő könyvtárban: .env, server.php, artisan

3.1.1 storage symlink létrehozása

A public könyvtár alatt szükség van egy symlink-re ami a storage könyvtárra mutasson.
Ez - például - a doc -ban lévő symlink.php -nek a public -ba feltöltésével és böngészöből futtatásával hozható létre. Sikeres futás után a symlink.php törlendő a szerverről.


### 3.2 Ha nincs lehetőségünk a document_root modositására:

könyvtár struktúra a document_root alatt:

```
    app/                 
    bootstrap/           
    config/
    database/
    resources/
    routes
    storage/
    vendor/
```

fájlok a fő könyvtárban: .env, server.php, artisan és a public könyvtár tartalmát (alkönyvtárakkal együtt, de a storage symlink nélkül) is a document_root -ba töltsük fel.

Az index.php -t modositsuk, töröljünk minden file utvonalból a "../" részt.

### Mindkét esetben
a "storage" könyvtár kivételével a többi könyvtár és file csak olvasható legyen.
a "storage" legyen irható is a web szerver számára. 

# project alapja 
[https://www.soengsouy.com/2020/12/login-with-laravel-8-and-socialite.html](https://www.soengsouy.com/2020/12/login-with-laravel-8-and-socialite.html)

(ez a link tartalmazza  a  facebook, goggle, github konfigurálási utmutatót is)



