# CarCare

CarCare je web aplikacija za praćenje održavanja vozila. Aplikacija korisniku omogućuje registraciju i prijavu, dodavanje vlastitih vozila, vođenje servisne povijesti, praćenje troškova održavanja te upravljanje podsjetnicima za buduće obveze vezane uz vozilo.

Projekt je izrađen kao projektni zadatak iz kolegija Web programiranje.

---

## Funkcionalnosti aplikacije

Aplikacija omogućuje:

- registraciju korisnika
- prijavu i odjavu korisnika
- zaštitu privatnih stranica pomoću PHP sesija
- dodavanje, prikaz i brisanje vozila
- dodavanje, prikaz, filtriranje i brisanje servisnih zapisa
- izračun ukupnog troška servisa
- dodavanje, prikaz, filtriranje, označavanje i brisanje podsjetnika
- dashboard sa statistikom korisnika
- prikaz najbližih aktivnih podsjetnika
- stranicu s izvorima i literaturom

---

## Korištene tehnologije

U projektu su korištene sljedeće tehnologije:

- HTML
- CSS
- JavaScript
- PHP
- MySQL
- XAMPP
- phpMyAdmin

---


## Baza podataka

Projekt koristi MySQL bazu podataka pod nazivom:

```txt
carcare_db
```

Baza se sastoji od četiri glavne tablice:

- `users`
- `vehicles`
- `service_records`
- `reminders`

### Tablica `users`

Tablica `users` sprema podatke o korisnicima aplikacije.

Glavna polja:

- `id`
- `name`
- `email`
- `password_hash`
- `created_at`

Lozinka se ne sprema kao običan tekst, nego se sprema hash lozinke.

---

### Tablica `vehicles`

Tablica `vehicles` sprema vozila koja je korisnik dodao.

Glavna polja:

- `id`
- `user_id`
- `brand`
- `model`
- `year`
- `license_plate`
- `mileage`
- `created_at`

Polje `user_id` povezuje vozilo s korisnikom. Na taj način svaki korisnik vidi samo svoja vozila.

---

### Tablica `service_records`

Tablica `service_records` sprema servisne zapise za vozila.

Glavna polja:

- `id`
- `vehicle_id`
- `service_type`
- `service_date`
- `mileage_at_service`
- `cost`
- `description`
- `created_at`

Polje `vehicle_id` povezuje servisni zapis s određenim vozilom.

---

### Tablica `reminders`

Tablica `reminders` sprema podsjetnike za vozila.

Glavna polja:

- `id`
- `vehicle_id`
- `title`
- `reminder_date`
- `description`
- `status`
- `created_at`

Polje `status` može imati vrijednost:

- `active`
- `completed`

Vrijednost `active` označava aktivan podsjetnik, a `completed` označava riješen podsjetnik.

---

## Pokretanje projekta

Za pokretanje projekta potrebno je imati instaliran XAMPP.

Koraci za pokretanje:

1. Pokrenuti XAMPP Control Panel.
2. Uključiti Apache.
3. Uključiti MySQL.
4. Projekt staviti u mapu:

```txt
C:\xampp\htdocs\carcare
```

5. Otvoriti phpMyAdmin:

```txt
http://localhost/phpmyadmin
```

6. Napraviti bazu podataka:

```txt
carcare_db
```

7. Uvesti SQL datoteku baze podataka ako postoji.
8. Otvoriti aplikaciju u pregledniku:

```txt
http://localhost/carcare/
```

---

## Način rada aplikacije

Aplikacija koristi PHP stranice za prikaz korisničkog sučelja, a JavaScript za dinamičko slanje i dohvaćanje podataka.

Podaci se šalju pomoću `fetch()` metode prema vlastitim PHP API datotekama u mapi `api/`.

Primjer tijeka rada:

1. Korisnik ispuni formu.
2. JavaScript napravi klijentsku validaciju.
3. Podaci se šalju na PHP API pomoću `fetch()`.
4. PHP API provjerava sesiju i podatke.
5. PHP API komunicira s MySQL bazom.
6. API vraća JSON odgovor.
7. JavaScript prikazuje rezultat na stranici bez ponovnog učitavanja cijele stranice.

---

## Sigurnosni elementi

U projektu su korišteni osnovni sigurnosni elementi.

### Hashiranje lozinki

Lozinke se pri registraciji spremaju pomoću funkcije:

```php
password_hash()
```

Kod prijave se lozinka provjerava pomoću funkcije:

```php
password_verify()
```

Na taj način se stvarne lozinke korisnika ne spremaju u bazu podataka.

---

### PHP sesije

Nakon uspješne prijave u sesiju se spremaju podaci o korisniku, primjerice:

```php
$_SESSION["user_id"]
$_SESSION["user_name"]
```

Privatne stranice zaštićene su pomoću datoteke:

```txt
includes/auth_check.php
```

Ako korisnik nije prijavljen, preusmjerava se na stranicu za prijavu.

---

### PDO Prepared Statements

Za rad s bazom koriste se PDO pripremljeni SQL upiti.

Primjer:

```php
$stmt = $pdo->prepare("
    SELECT id, name, email, password_hash
    FROM users
    WHERE email = :email
    LIMIT 1
");

$stmt->execute([
    "email" => $email
]);
```

Prepared statements smanjuju rizik od SQL injection napada jer se korisnički unos ne spaja izravno u SQL naredbu.

---

### XSS zaštita

Kod dinamičkog prikaza korisničkih podataka u JavaScriptu koristi se funkcija:

```javascript
function escapeHtml(value) {
    return String(value)
        .replaceAll("&", "&amp;")
        .replaceAll("<", "&lt;")
        .replaceAll(">", "&gt;")
        .replaceAll('"', "&quot;")
        .replaceAll("'", "&#039;");
}
```

Ova funkcija smanjuje rizik od XSS napada jer se posebni HTML znakovi pretvaraju u sigurne HTML entitete.

---

### Autorizacija korisnika

Kod dohvaćanja i brisanja podataka provjerava se pripada li podatak prijavljenom korisniku.

Primjer kod brisanja vozila:

```sql
DELETE FROM vehicles
WHERE id = :vehicle_id
AND user_id = :user_id
```

Kod servisa i podsjetnika provjera se radi preko veze s tablicom `vehicles`, jer servisni zapisi i podsjetnici pripadaju vozilima.

---

## Glavne stranice

### `index.php`

Početna stranica aplikacije. Prikazuje osnovni opis aplikacije i gumbe za prijavu/registraciju ili gumbe za pristup aplikaciji ako je korisnik već prijavljen.

### `register.php`

Stranica za registraciju korisnika.

### `login.php`

Stranica za prijavu korisnika.

### `dashboard.php`

Nadzorna ploča koja prikazuje statistiku korisnika:

- broj vozila
- broj servisnih zapisa
- ukupan trošak servisa
- broj aktivnih podsjetnika
- najbliže aktivne podsjetnike

### `vehicles.php`

Stranica za upravljanje vozilima. Korisnik može dodati, pregledati i obrisati vozila.

### `services.php`

Stranica za vođenje servisne povijesti. Korisnik može dodati servisni zapis, pregledati servise za odabrano vozilo, filtrirati ih po vrsti i pratiti ukupan trošak.

### `reminders.php`

Stranica za upravljanje podsjetnicima. Korisnik može dodati podsjetnik, pregledati podsjetnike po vozilu, filtrirati ih po statusu, označiti podsjetnik kao riješen i obrisati ga.

### `sources.php`

Stranica s korištenim izvorima, dokumentacijom i sigurnosnim konceptima.

---

## Korišteni izvori

U projektu su korišteni izvori vezani uz:

- PHP dokumentaciju
- MySQL dokumentaciju
- MDN Web Docs
- XAMPP
- phpMyAdmin
- OWASP XSS Prevention
- OWASP SQL Injection Prevention
- PHP PDO Prepared Statements
- PHP `password_hash()`

Izvori su navedeni i objašnjeni na stranici `sources.php`.

---

## Autor

Dominik Perić 