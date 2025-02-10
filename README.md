WordPress web (téma, pluginy, konfigurácia) pre salezinani.sk. Inštalácia si vyžaduje aspoň základné znalosti PHP a node.js.

Tému je možné použiť aj na weby saleziánskych stredísk, ale v ďalšom vývoji témy bude potrebné upraviť niektoré časti kódu a doplniť funkcionality. Ak máš nejaký nápad, neváhaj a vytvor issue alebo ideálne aj pull request.

# Inštalácia

1. Naklonuj si repozitár
2. vytvor kópiu súboru `.env.example`, premenuj ho na `.env.local` a nastav v ňom prístupové údaje k databáze a iné konfiguračné premenné
3. spusti `composer install` (PHP verzia 8.3.x)
4. spusti db migrácie `bin/console doctrine:migration:migrate -n`
5. spusti `npm install` (node verzia 18.x)
6. spusti `npm run dev` (vývojový režim) alebo `npm run prod` (produkčný režim) na prekompilovanie js/scss assetov

Pri vývoji môžeš použiť dev web server spustením `symfony serve`.

# Zásady vývoja

1. S pluginmi veľmi opatrne
   - Nepoužívame pluginy na triviálne veci alebo veci, ktoré sa dajú jednoducho vyriešiť vlastným kódom.
   - Ak je fakt potrebné použiť plugin, tak ho pridaj do composer.json (mirror repozitár WP pluginov inštalovateľných cez composer nájdeš tu: https://wpackagist.org/)
2. Zdroj pravdy je kód, nie databáza
   - Všetky pluginy, custom databázové tabuľky, či iné závislosti musia byť súčasťou kódu a nie vyklikané cez administráciu, aby bolo možné jednoducho spraviť clean install webu (napr. pre dev prostredie), v rámci CI/CD.
3. Dobrý a prehľadný kód
   - Snažíme sa písať dobrý a prehľadný kód.
   - Snažíme sa o "WordPress way" implementácie funkcionalít a vlastné postupy zavádzame, keď prinášajú výraznú výhodu (napr. dependency injection, db migrácie, a pod.).
   - Snažíme sa o best practices pre PHP, JS a SCSS.
4. Otvorenosť spätnej väzbe a kritike
   - Konštruktívna kritika je veľmi vítaná. Umožňuje nám zlepšovať výsledný produkt a zlepšovať naše schopnosti.
   - Ak máš nejaký nápad na novú funkcionalitu alebo opravu, vytvor issue alebo aj pull request.
