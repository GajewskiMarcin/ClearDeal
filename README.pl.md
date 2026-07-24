# ClearDeal — Najniższa cena (Dyrektywa Omnibus) dla PrestaShop

[![PrestaShop](https://img.shields.io/badge/PrestaShop-8.x%20%7C%209.x-blue)](https://www.prestashop.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-777bb4)](https://www.php.net/)
[![License](https://img.shields.io/badge/License-AFL--3.0-green)](LICENSE)
[![Release](https://img.shields.io/github/v/release/GajewskiMarcin/ClearDeal?include_prereleases)](https://github.com/GajewskiMarcin/ClearDeal/releases)

**ClearDeal** śledzi historię cen produktów i wyświetla **najniższą cenę z ostatnich N dni** obok ceny aktualnej — czyli informację wymaganą przez **dyrektywę Omnibus** (2019/2161) przy każdym ogłoszeniu obniżki ceny.

🇬🇧 [English README](README.md)

---

## Co robi moduł

Przy ogłaszaniu promocji przepisy wymagają podania najniższej ceny, jaka obowiązywała w okresie przed obniżką (co najmniej 30 dni). ClearDeal obsługuje cały ten proces automatycznie:

1. **Zapisuje** każdą zmianę ceny do własnej tabeli w bazie (produkt, wariant, sklep, waluta, kraj, grupa klientów).
2. **Wylicza** najniższą cenę w skonfigurowanym okresie.
3. **Wyświetla** ją na karcie produktu, listingach kategorii i w quick view — opcjonalnie z procentem obniżki, tooltipem i interaktywnym wykresem cen.

Zero pracy ręcznej, zero arkuszy kalkulacyjnych.

---

## Funkcje

### Śledzenie cen
- Automatyczne logowanie przy dodaniu produktu, aktualizacji produktu oraz dodaniu / edycji / usunięciu ceny promocyjnej (specific price).
- Dwa tryby logowania: **natychmiastowy** (na hookach) lub **wyłącznie CRON**.
- Historia wielowymiarowa: per **sklep**, **waluta**, **kraj**, **grupa klientów** i **wariant** — dzięki czemu multistore i ceny per kraj pozostają poprawne.
- Konfigurowalna **retencja logów** (30–3650 dni) z automatycznym czyszczeniem raz na dobę.
- **Import / eksport CSV** historii cen oraz akcja masowego zalogowania wszystkich cen.

### Wyświetlanie
- Najniższa cena z konfigurowalnego okresu (**1–365 dni**, domyślnie 30).
- Tryb wyświetlania: **zawsze** lub **tylko gdy produkt jest przeceniony**.
- Ceny **brutto lub netto**, z regulowaną precyzją (0–4 miejsca po przecinku).
- Opcjonalny **procent obniżki** względem najniższej ceny.
- Osobne, **w pełni tłumaczalne etykiety** dla karty produktu, listingu i quick view.
- **Ikona informacyjna + tooltip** — dowolna ikona Bootstrap lub własny wgrany obrazek, przed lub za tekstem.

### Wykres historii cen
- Interaktywne okno modalne (**Chart.js**) pokazujące przebieg zmian ceny.
- Wyzwalane kliknięciem w ikonę, w cenę lub w dowolne miejsce bloku.
- Konfigurowalne kolory linii, wypełnienia i nagłówka.

### Wygląd
- Trzy **presety stylów**: `Domyślny` (tło + ramka), `Minimalny` (bez tła), `Wyrazisty` (gradient).
- Personalizacja kolorów dla każdego presetu oraz pole **własnego CSS**.
- Automatyczne wyliczanie kontrastu tekstu w tooltipie.

### Integracja
- Działa od razu na standardowych hookach: `displayProductPriceBlock`, `displayProductAdditionalInfo`, `displayProductListReviews`.
- **Własne hooki** dla pełnej kontroli nad umiejscowieniem:
  `displayClearDealProduct`, `displayClearDealListing`, `displayClearDealQuickView`.
- Widget do page buildera **Creative Elements** (kategoria *Secret Sauce*).
- **Wykluczanie kategorii** — pomiń produkty, których nie chcesz śledzić ani wyświetlać.
- **24 tłumaczenia** w komplecie.

---

## Wymagania

| | |
|---|---|
| PrestaShop | 8.0 – 9.x |
| PHP | 8.1 lub nowszy |
| MySQL | 5.6+ / MariaDB 10.1+ |

---

## Instalacja

### Z wydania (zalecane)

1. Pobierz `cleardeal.zip` ze strony [Releases](https://github.com/GajewskiMarcin/ClearDeal/releases).
2. W panelu PrestaShop przejdź do **Moduły → Menedżer modułów → Wgraj moduł**.
3. Wybierz plik ZIP i zainstaluj.
4. Otwórz konfigurację modułu (lub **Ulepszenia → Secret Sauce → ClearDeal**).

### Ze źródeł

```bash
git clone https://github.com/GajewskiMarcin/ClearDeal.git
cp -r ClearDeal/cleardeal /sciezka/do/prestashop/modules/
```

Następnie zainstaluj moduł z Menedżera modułów.

> **Ważne:** zaraz po instalacji historia cen jest pusta. Poczekaj na zmiany cen, uruchom jednorazowo **Historia cen → Zaloguj wszystkie ceny teraz**, aby utworzyć punkt odniesienia, albo zaimportuj istniejącą historię z pliku CSV.

---

## Konfiguracja

Konfiguracja modułu ma cztery zakładki:

| Zakładka | Zawartość |
|---|---|
| **Ustawienia** | Okres w dniach, tryb wyświetlania, brutto/netto, procent, precyzja, wykres, wykluczone kategorie, etykiety, ikona i tooltip, tryb logowania, retencja |
| **Wygląd** | Preset stylu, kolory, własny CSS |
| **Historia cen** | Statystyki, przeglądarka logów z filtrami, import/eksport CSV, czyszczenie, ręczne logowanie |
| **O module i wsparcie** | Informacje o wersji i odnośniki |

### Ręczne umieszczenie bloku

Jeśli Twój szablon wymaga bloku w konkretnym miejscu, użyj jednego z własnych hooków:

```smarty
{hook h='displayClearDealProduct' product=$product}
{hook h='displayClearDealListing' product=$product}
{hook h='displayClearDealQuickView' product=$product}
```

---

## Konfiguracja CRON

Jeśli ustawisz **tryb logowania** na *wyłącznie CRON* (zalecane przy dużych katalogach), zaplanuj wywołanie adresu:

```
https://twoj-sklep.pl/module/cleardeal/cron?token=TWOJ_TOKEN&action=all
```

Dokładny adres z wygenerowanym tokenem znajdziesz w zakładce **Ustawienia** modułu.

| `action` | Działanie |
|---|---|
| `log` | Zapisuje aktualne ceny wszystkich aktywnych produktów |
| `clean` | Usuwa logi starsze niż okres retencji |
| `all` (domyślne) | Obie powyższe operacje |

Przykładowy wpis w crontabie (codziennie o 3:00):

```cron
0 3 * * * curl -s "https://twoj-sklep.pl/module/cleardeal/cron?token=TWOJ_TOKEN&action=all" > /dev/null
```

---

## Przechowywanie danych

Moduł tworzy jedną tabelę, `PREFIX_cleardeal_price_history`, przechowującą po jednym wierszu na zarejestrowany punkt cenowy (cena brutto i netto, sklep, waluta, kraj, grupa, znacznik czasu). Tabela jest zindeksowana pod szybkie wyszukiwanie najniższej ceny i zostaje usunięta przy odinstalowaniu modułu, razem z wszystkimi ustawieniami.

Żadne dane nie są wysyłane poza Twój sklep. Jedyne zewnętrzne zasoby ładowane na front to Bootstrap Icons oraz — gdy wykres jest włączony — Chart.js, oba z CDN jsDelivr.

---

## Nota prawna

ClearDeal jest narzędziem technicznym, które rejestruje i prezentuje historię cen. Nie stanowi porady prawnej, a prawidłowa zgodność z dyrektywą Omnibus zależy również od sposobu konfiguracji (długość okresu, wykluczone produkty, moment rozpoczęcia śledzenia). Zweryfikuj swoje ustawienia z przepisami obowiązującymi w Twoim kraju.

---

## Rozwój

Zgłoszenia i pull requesty są mile widziane. Zgłaszając błąd, podaj wersję PrestaShop, wersję PHP oraz używany szablon.

---

## Licencja

Udostępniony na licencji [Academic Free License 3.0 (AFL-3.0)](LICENSE) — licencji stosowanej dla modułów PrestaShop.

## Autor

**Marcin Gajewski** — [marcingajewski.pl](https://marcingajewski.pl)
