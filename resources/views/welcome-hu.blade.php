<div id="leiras">
<h2>Leírás</h2>


<div class="leirasBody">
	         <!-- img src="{{ URL::to('/') }}/img/logo.png" 
	            	style="display:inline-block; float:left; margin:0 10px 0 10px; width:300px " / -->
	         <h2>Áttekintés</h2>   	
			
<a name="description"></a>
				<h3>
				A Netpolgár egy virtuális közösségi tér. E-demokrácia, projekt menedzser,
				virtuális piactér, fájl tároló és kommunikációs felület.</h3>
				<h4>Ez egy pártoktól,
				szervezetektől független civil kezdeményezés.</h4>
				<p>Célja egy alternatív hierarchia mentes, egyenrangú autonóm
				egyedekből álló együttműködési rendszer kimunkálása,
				gyakorlati kipróbálása. 
				</p>
				<p>
					Készültség: <strong>V0.0.7 ß teszt</strong>
    				<ul>
        				<li>programozás: 90%</li>
        				<li>dizájn: 1%</li>
        				<li>tesztelés: 10%</li>
    				</ul>
				</p>
				<div style="float:right; width:auto">
					<h4>Teszt bejelentkezések:</h4>
					<ul>
						<li>teszt1@teszt.hu psw: 12345678</li>					
						<li>teszt2@teszt.hu psw: 12345678</li>					
						<li>teszt3@teszt.hu psw: 12345678</li>					
					</ul>				
				</div>
				<div class="tartalom">


				@include('help.home')
<!--
<a name="description1"></a>				
					<h4>Tartalomjegyzék</h4>
					<ul>
						<li><p><a href="#fojellemzok" onclick="true">Fő jellemzők</a></p>
						<li><p><a href="#csoportok" onclick="true">Csoport szervezés</a> (ß teszt) </p>
						<li><p><a href="#projektek" onclick="true">Projekt	menedzselés (ß teszt)</a></p>
						<li><p><a href="#piacter" onclick="true">Virtuális piactér</a> (ß teszt)</p>
						<li><p><a href="#esemenyek" onclick="true">Esemény	szervezés</a> (ß teszt)</p>
						<li><p><a href="#vitak" onclick="true">Eszmecserék, viták lebonyolítása</a> (ß teszt)</p>
						<li><p><a href="#szavazasok" onclick="true">Szavazások lebonyolítása</a> (ß teszt)</p>
						<li><p><a href="#uzenetek" onclick="true">Kommunikáció</a> (ß teszt)</p>
						<li><p><a href="#fileok" onclick="true">File könyvtár</a> (ß teszt)</p>
					</ul>
				</div>
<a name="fojellemzok"></a>
				<p>&nbsp;</p>
				<h5>Fő jellemzők</h5>
				<div>
				<p>Minden felhasználónak  van fájl	tároló területe. A felhasználók csoportokat hozhatnak létre,
				csatlakozhatnak mások által létrehozott csoportokhoz (a
				csatlakozás rendjét a csoport szabályzata határozza meg). A
				csoportoknak is van file tárolójuk.
				Ezek a csoport tagjai számára elérhetőek. A csoportok projekteket
				(közösen megvalósított munkafolyamatokat, célkitűzéseket)
				indíthatnak. A projekteknek is van file tároló területük.
				Ezek a projekt-gazda csoport tagjai számára
				elérhetőek.  A felhasználók privát üzeneteket
				is küldhetnek egymásnak, üzenetet küldhet egy csoport összes
				tagjának, projekt résztvevőknek, eseményre jelentkezőknek is.
				Szellemi és anyagi termékek	cseréjét, megosztását virtuális, 
				alternatív fizetőeszközös web-es piactér segíti. 
				A felhasználók NET -es likvid demokrácia szerinti szavazásokat
				indíthatnak, ezek rendelhetőek csoporthoz, projekthez,
				kommentelhetnek, értékelhetnek, szavazhatnak. A rendszerhez bárki
				önként csatlakozhat, ingyenesen használhatja azt.</p>
<a name="csoportok"></a>
				<p>&nbsp;</p>
				<h5>Csoport szervezés (ß teszt)</h5>
				<p>A netpolgár
				programban a hasonló érdeklődésű tagok vagy egy projekten
				közösen dolgozó tagok számára; hierarchikus „fa szerkezetű”
				csoport struktúra alakítható ki (fő csoport – csoport –
				alcsoport stb). Egy felhasználó több csoportban is tag lehet. A
				csoport tagok a program segítségével „chatelhetnek” egymással,
				videó konferenciákat bonyolíthatnak le, vitákat folytathatnak,
				szavazásokat rendezhetnek.</p>
				<p><br/></p>
				<p><i>( Megjegyzés: a csoport
				szerkezet hierarchikus felépítése a csoportok közötti könnyebb
				eligazodást segíti, illetve később a likvid demokrácia
				kialakításánál lesz szerepe)</i></p>
				<p><br/></p>
				<p>A felhasználók
				”jelentkezhetnek” csoport tagnak. A teljes jogú tagsághoz az
				szükséges, hogy a csoport már meglévő tagjai közül – a
				csoport beállításainál megadott számú – tag támogassa az új
				tag belépését. 
				</p>
				<p><br/></p>
				<p><i>(Megjegyzés: ha
				a csoport létszám kisebb a beállított támogatási szükségletnél,
				akkor a tagok 100% -os támogatása szükséges. Nulla „szükséges
				támogatás” beállításánál a jelentkezők azonnal teljes jogú
				tagok lesznek.)</i></p>
				<p><br/></p>
				<p>A csoport tagjai
				javasolhatják tag kizárását. A tag akkor lesz kizárva ha a
				csoport tagok – a csoport beállításainál %-ban megadott –
				hányada javasolta a kizárást. 
				</p>
				<p><br/></p>
				<p>A csoportokban
				alcsoportok hozhatóak létre. Alcsoport létrehozását a csoport
				tagok javasolhatnak. A csoport akkor nyílik meg ha a javaslat a
				csoport tagoktól megkapja - a csoport beállításoknál megadott –
				szükséges támogatást. A javasolt csoportban chatelni, vitákat,
				szavazásokat szervezni, alcsoportokat szervezni csak a szükséges
				támogatás elérése után lehet.</p>
				<p><br/></p>
				<p><i>(Megjegyzés: ha
				a csoport létszám kisebb a beállított támogatási szükségletnél,
				akkor a tagok 100% -os támogatása szükséges. Nulla „szükséges
				támogatás” beállításánál az alcsoport</i> <i>azonnal
				megnyílik.)</i></p>
				<p><br/></p>
				<p>A csoport tagjai
				javasolhatják a csoport lezárását. A csoport akkor lesz lezárva
				ha a tagok –  a csoport beállításainál %-ban megadott –
				hányada javasolta a lezárást.
				</p>
				<p><br/></p>
				<p>Minden csoportnak
				van legalább egy „adminisztrátora” alapértelmezetten a
				csoportot javasló felhasználó az adminisztrátor. Az
				adminisztrátor moderálhatja a csoportban lévő beszélgetéseket,
				módosíthat, javíthat a vita kérdések és a hozzájuk tartozó
				alternatívák szövegein, módosíthatja a csoport nevét, leírását
				és beállításait. 
				</p>
				<p><br/></p>
				<p>A csoport aktiválása után bármelyik csoport tag jelentkezhet adminisztrátornak. A tisztséget
				akkor töltheti be ha a csoport tagok – a csoport beállításnál
				%-ban megadott – hányada támogatja azt. 
				</p>
				<p><br/></p>
				<p>A csoport tagok
				kezdeményezhetik valamelyik adminisztrátortól a az adminisztrálási
				jog megvonását. A jog akkor kerül megvonásra ha a csoport tagok –
				a beállításoknál %-ban megadott – hányada ezt javasolta.</p>
				<p><br/></p>
				<p>A csoportokban
				egyéb tisztségek (pl. vita moderátor, képviselő, szóvivő stb)
				is lehetnek, ezek kezelése az adminisztrátori tisztséghez hasonló.</p>
				<p><br/></p>
				<p>A legfelső szintű
				csoportokat a rendszer adminisztrátor kezeli. Van egy különleges
				működésű csoport a „<b>Regisztrált felhasználók</b>” ennek
				automatikusan tagja mindenki aki regisztrál a programba.</p>
				<p><br/></p>
				</div>
<a name="projektek">
				<p>&nbsp;</p>
				<h5></a>Projekt menedzselés (ß teszt)</h5>
				<div>
				<p>A rendszer adminisztrátorok, csoport adminisztrátorok
				projekteket indíthatnak. A projektek egy csoporthoz is
				kapcsolódhatnak. A projektek témakörökhöz rendelhetőek. Minden
				projektnek van legalább egy projekt adminisztrátora. A projekt
				adminisztrátorok résztvevőket vehetnek
				fel/módosíthatnak/törölhetnek, meghívókat küldhetnek. A
				regisztrált felhasználók résztvevőnek jelentkezhetnek a
				projektbe. A jelentkezést projekt adminisztrátornak kell jóvá
				hagynia. A projekt tagok bármikor kiléphetnek a projektből. A
				projekt adminisztrátorok feladatokat (taszkokat) definiálhatnak a
				projektbe, határidőt, felelőst adhatnak meg a feladathoz.
				Módosíthatják a feladat állapotát. A projekt adminisztrátorok
				lezárhatják, felfüggeszthetik a projektet. A projekt felvitelekor
				a felvivő lesz az adminisztrátor, később további
				adminisztrátorokat is kijelölhet a résztvevők közül. A rendszer
				adminisztrátor törölhet projekteket.</p>
				</div>
<a name="piacter">
				<p>&nbsp;</p>
				<h5></a>Virtuális piactér (ß teszt)</h5>
				<div>
				<p>A csoport adminisztrátorok, regisztrált felhasználók kínálatokat vihetnek fel.
				A kínálatok témakörökhöz rendelhetőek. 
				A kínálatok csoporthoz vagy felhasználóhoz kapcsolódhatnak. A
				felhasználások (tranzakciók) virtuális pénzben történő
				elszámolással vihetők fel a rendszerbe. A virtuális pénz neve: NTC (NET coint).
				Kiindulásként, első közelítésként; ajánlott 1NTC -t egy óra munka ellenértékének
				tekinteni. A "vásárló/felhasználó" lehet egy csoport, vagy egy felhasználó. 
				Csoport nevében a csoport adminisztrátorok vásárolhatnak. 
				Vásárláskor a "vevő" NTC egyenlege csökken,	az "eladó" NTC egyenlege növekszik.
				</p>
				<p>
				A főmenü "piactér" menüpontjábam a felhasználók saját nevükben tehetnek fel kinálatokat,
				 a csoport almenü "Termékek" menüpontjában a csoport adminisztrátorok a csoport nevében
				 tölthetnek fel kinálatokat.
				</p>
				<p>A vásárlás a web áruházakban megszokott módon történik, a vevő a "kosarába" gyűjti
				a kiválasztott termékeket/szolgáltatásokat majd elküldi a megrendelést. Ha a felhasználó 
				csoport	adminisztrátor akkor ki kell választania, hogy saját nevében vagy valamelyik 
				általa menedzselt csoport nevében vásárol.</p>
				<p>A megrendelés elküldésekor zárolásra kerül a "vásárló" folyószámláján a rendelt
				termékek, szolgáltotások ára. Teljesítéskor kerül át az összeg az "eladó" számlájára.
				Ha a tranzakció bármilyen okból meghiúsul, akkor a zárolt összeg felszabadul a 
				"vevő" számláján.</p>
				</div>
				<p><strong>JELEN TESZT VÁLTOZATBAN minden felhasználó kap 500 NTC induló keretet, 
				és a folyószámla tetszőleges mértékben minuszba is mehet.</strong>
				Késöbb ez változni fog.
				</p>
<a name="esemenyek"></a>
				<p>&nbsp;</p>
				<h5>Esemény szervezés (ß teszt)</h5>
				<div>
				<p>A projekt adminisztrátorok, csoport adminisztrátorok,
				regisztrált felhasználók eseményeket vihetnek fel. Az események
				témakörökhöz rendelhetőek. Az események csoporthoz vagy
				projekthez kapcsolódhatnak. Az esemény szervező email-ben
				meghívókat küldhet ki. A regisztrált felhasználók
				jelentkezhetnek az eseményre. Az esemény felvitelekor a felvívő
				lesz az adminisztrátor, később további adminisztrátorokat is
				kijelölhet. Szükség esetén az eseményen történő tényleges
				részvétel (beleértve a virtuális részvételt is) is
				adminisztrálható.</p>
				</div>
<a name="vitak"></a>
				<p>&nbsp;</p>
				<h5>Eszmecserék, viták
				lebonyolítása (ß teszt)</h5>
				<div>
				<p>A csoport tagok
				az eldöntendő kérdésekről viták indítását javasolhatják. A
				vita akkor indul meg ha – a csoport beállításainál megadott –
				számú tag támogatja azt. A vitát javasló tag megoldási
				lehetőségeket (alternatívákat) is javasolhat. 
				</p>
				<p><br/></p>
				<p><i>(Megjegyzés: ha
				a csoport létszám kisebb a beállított támogatási szükségletnél,
				akkor a tagok 100% -os támogatása szükséges. Nulla „szükséges
				támogatás” beállításánál a vita azonnal megindul)</i></p>
				<p><br/></p>
				<p>A megindult viták
				a vita beállításainál megadott vita idő letelte után szavazássá alakulnak.</p>
				<p>Az aktív
				vitákhoz a tagok hozzászólhatnak, és megoldási alternatívákat
				javasolhatnak. A csoport tagok támogathatják az egyes megoldási
				javaslatok „virtuális szavazó lapra kerülését”. <b><strong>Tehát
				itt még nem arról van szó, hogy ezt a megoldást támogatod, hanem
				csak arról, hogy a későbbi szavazásnál ez szerepeljen-e a
				virtuális szavazó lapon.</strong>(kb. a jelenlegi "képviselő ajánlás" megfelelője) </b>Egy
				megoldási lehetőség akkor kerül fel a „virtuális szavazó
				lapra” ha megkapta a vita beállításinál megadott számú
				támogatást.</p>
				<p><br/></p>
				<p>
				A vita közben a csoport adminisztrátorok és a vitát kezdeményező
				felhasználó még módosíthat, javíthat a vita és a megoldási
				lehetőségek szövegein, és a vita/szavazás beállításain.
				(etikai szempontból csak helyesírási, fogalmazási hibák javítása
				javasolt) A szavazás során és a szavazás lezárulta után
				módosítás már nekik sem engedélyezett.</p>
				<p><br/></p>
				<p><i>(Megjegyzés: értelemszerűen az Igen/nem típusú szavazásnál
				megoldási lehetőséget nem lehet javasolni)</i></p>
				<p><br/></p>
				<p>
				A vita beállításainál megadott napig tartó vita után indul meg a
				szavazás, ami ugyancsak a beállításoknál megadott ideig tart.</p>
				<p><br/></p>
				</div>
<a name="szavazasok"></a>
				<p>&nbsp;</p>
				<h5>Szavazások
				lebonyolítása (ß teszt)</h5>
				<div>
				A szavazás lehet:</p>
				<ul>
					<li><p>Igen/nem</p>
					<li><p>Egy választható</p>
					<li><p>Több választható</p>
					<li><p>Sorba rendező</p>
				</ul>
				<p>A szavazás lehet titkos vagy nyílt.</p>
				<p>A szavazás lehet "likvid" müködésű is.<br />
				<a href="https://alternativgazdasag.fandom.com/wiki/Likvid_demokr%C3%A1cia" target="_new">lásd itt</a></p>

				<p>A szavazás közben
				folyamatosan megtekinthetőek a rész eredmények és lekérhető a
				már megadott szavazatokat tartalmazó CSV fájl (ez nem tartalmaz a
				szavazatot leadó személy azonosítására alkalmas adatot). Ezen
				CSV fájl segítségével független ellenőrök ellenőrizhetik a
				szavazat összesítés helyességét, illetve a szavazás különböző
				időpontjaiban lekért CSV fájlok összevetésével vizsgálhatják,
				hogy esetleg történt-e visszamenőleges módosítás az
				adatbázisban.</p>
				<p><br/></p>
				<p>A szavazás során a
				szavazó képernyőjén megjelenik egy szavazat azonosító szám.
				Ezt a számot senki más nem ismeri csak aki a szavazatot leadta. Ha
				feljegyzi ezt a számot, ennek segítségével később bármikor
				ellenőrizheti, hogy a szavazata szerepel-e az adatbázisban (és a CSV
				fájlban), és a helyes tartalommal szerepel-e?</p>
				<p><br/></p>
				<p>A sorba rendező
				szavazás kiértékelése Condorcet – Schulze metod szerint
				történik.</p>
				<p>Ha a szavazás "likvid" tipusú, akkor; - ha egy felhasználó nem szavaz, 
				akkor helyette az adott csoportban vagy projektben általa kijelőlt 
				"képviselője" szavazatát vesszük figyelembe. Tehát a 
				"képviselők" szavazata akkor sullyal esik számtásba ahány
				őket meghatalmazó felhasználó nem szavaztott.</p>
				<p>
				<p>A szavazati jogot olyan tagra lehet átruházni aki erre 
				vállakozott. Ez a "képviseletre vállakozás" a "Tagok" képernyőn a "Képviselő tisztségre vállalkozok"
				gomb megnyomásával történik.</p>
				<p>Ha csopotban inditott szavazásról van szó, és a nem szavazó 
				felhasználónak az adott csoportban nincs képviselője, vagy 
				az sem szavazott; - akkor a felsőbb szintű csoportban lévő képviselője
				szavazatát használjuk. Ha ott sincs képviselő vagy az sem
				szavazott; - akkor még feljebb lévő csoportban lévőt és így tovább.</p>
				<p>A képviseleti megbízáss (meghatalmazás) a csoport vagy projekt "Tagok"
				menüpontjában lévő "Tisztségviselők" linkre kattintással elérhető 
				képernyőn a képviseletre válalkozó nevére vonatkozó "like" -al
				történik. A "pipa" ikon jelzi jelenleg ki a képviselőd. Ugyanitt a 
				képviseleti megbízást vissza is lehet vonni</p> 
				</div>
<a name="uzenetek"></a>				
				<p>&nbsp;</p>
				<h5>Üzenetek (ß teszt)</h5>
				<p>Lehetőség van szöveges üzenetet, megjegyzést írni</p>
				<ul>
				<li>Másik felhasználónak</li>
				<li>Csoportnak</li>
				<li>Projeknek</li>
				<li>Projekt feladathoz<li>
				<li>Termékhez, szolgáltatáshoz</li>
				</ul>
				<p>Ha a web böngésző támogatja a mikrofon és kamera 
				használatát akkor video-chat is lebonyolítható.</p>

				<strong>Privát üzenet küldése egy felhasználónak</strong>
				
				<ul>
					<li>A csoportok / regisztrált felhasználók / tagok menüpontjában 
						megkeressük a felhasználót.</li>
					<li>A megjelenő üzenetküldő formba beirjuk az üzenetet vagy 
						a "mikrofon" + "kamera" ikonra kattintva vide chat-et inditunk</li>
				</ul>
				
				<strong>Privát üzenet fogadása</strong>

				A bejelentkezés után a felső menű jobb oldalán lévő "privát üzenetek" 
				menüontban olvashatjuka nekünk küldött üzeneteket vagy csatlakozhatunk
				a privát vide-chat -hez.
				
				<strong>Üzenet küldése csoportnak, projekt résztvevőknek, 
					eseményre jelentkezőknek</strong>
				
					A csoportok / projekt / esemény menüjében lévő 
					"beszélgetések" menüpontban 
					megjelenő üzenetküldő formba beirjuk az üzenetet vagy 
					a "mikrofon" + "kamera" ikonra kattintva vide chat-et inditunk,
				
				<strong>Csoport, projekt, esemény üzenet fogadása</strong>
				
				A csoportok / projekt / esemény menüjében lévő 
					"beszélgetések" menüpontban olvashatjuk az üzeneteket vargy 
					csatlakozhatunk a video-chat -hez.

<a name="fileok"></a>
				<p>&nbsp;</p>
				<h5>File könyvtár (ß teszt)</h5>
				<p>A csoportokhoz és minden regisztrált felhasználóhoz tartozik egy-egy file könyvtár.</p>
				<p>A csoport file könyvtárába a csoport tagok tölthetnek fel fájlokat, 
				modosítani, törölni a feltöltő és a csoport adminisztrátorok tudnak.</p>
				<p>A felhasználó könyvtárába csak a felhasználó tölthet fel, ő módosíthat, törölhet.</p>
				<p>A feltöltött fájlok listája minden látogató számára látható, letölteni a 
				regisztrált felhasználók tudnak.</p>
				<p>A feltölthető fájlok méretét a szerver beállításai határozzák meg (uload limit és
				post data méret). Az összes feltöltendő fájl méretét a fizikailag rendelkezésre 
				álló tároló kapacitás korlátozza</p>
				<p>Bizronsági okokból php, html, html, js fájlok feltöltése nem engedélyezett.</p>
-->								
	</div>
</div>

