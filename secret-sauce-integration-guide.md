# Secret Sauce - Instrukcja podpinania modułów

## Koncepcja

"Secret Sauce" to współdzielona kategoria w menu PrestaShop (pod sekcją IMPROVE), do której różne moduły mogą dodawać swoje linki. Kategoria nie ma właściciela (`module = ''`), więc nie zostanie usunięta gdy jeden z modułów zostanie odinstalowany.

---

## Kod do skopiowania

W metodzie `installTabs()` nowego modułu dodaj:

```php
private function installTabs()
{
    // ========================================
    // KROK 1: Znajdź lub utwórz "Secret Sauce"
    // ========================================

    // Znajdź sekcję IMPROVE (lub fallback)
    $improve_id = (int)Tab::getIdFromClassName('IMPROVE');
    if (!$improve_id) {
        $improve_id = (int)Tab::getIdFromClassName('AdminParentModulesSf');
    }
    if (!$improve_id) {
        $improve_id = 0;
    }

    // Sprawdź czy Secret Sauce już istnieje
    $secret_sauce_class = 'AdminSecretSauce';
    $secret_sauce_id = (int)Tab::getIdFromClassName($secret_sauce_class);

    // Jeśli nie istnieje - utwórz
    if (!$secret_sauce_id) {
        $secretSauce = new Tab();
        $secretSauce->active = 1;
        $secretSauce->class_name = $secret_sauce_class;
        $secretSauce->id_parent = $improve_id;
        $secretSauce->module = ''; // WAŻNE: Brak właściciela!
        if (property_exists($secretSauce, 'icon')) {
            $secretSauce->icon = 'science'; // Material icon (kompatybilny z PS8 i PS9)
        }
        foreach (Language::getLanguages(false) as $lang) {
            $secretSauce->name[(int)$lang['id_lang']] = 'Secret Sauce';
        }
        if (!$secretSauce->add()) {
            return false;
        }
        $secret_sauce_id = (int)$secretSauce->id;
    }

    // ========================================
    // KROK 2: Dodaj tab(y) swojego modułu
    // ========================================

    $tabs = array(
        // Główny widoczny link w menu
        array(
            'class_name' => 'AdminMojModulMain',      // Zmień na swój
            'name' => 'Mój Moduł',                    // Zmień na swój
            'id_parent' => $secret_sauce_id,          // Pod Secret Sauce
        ),
        // Ukryte taby (dostępne przez nawigację wewnętrzną)
        array(
            'class_name' => 'AdminMojModulSettings',  // Zmień na swój
            'name' => 'Mój Moduł Settings',           // Zmień na swój
            'id_parent' => -1,                        // -1 = ukryty w menu
        ),
    );

    foreach ($tabs as $t) {
        $existingId = Tab::getIdFromClassName($t['class_name']);
        if ($existingId) {
            // Aktualizuj istniejący tab
            $tab = new Tab($existingId);
            $tab->id_parent = (int)$t['id_parent'];
            $tab->active = 1;
            $tab->save();
            continue;
        }

        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = $t['class_name'];
        $tab->module = $this->name;  // Twój moduł jest właścicielem tego taba
        $tab->id_parent = (int)$t['id_parent'];

        foreach (Language::getLanguages(false) as $lang) {
            $tab->name[(int)$lang['id_lang']] = $t['name'];
        }

        if (!$tab->add()) {
            return false;
        }
    }

    return true;
}
```

---

## Kod uninstallTabs()

```php
private function uninstallTabs()
{
    // Usuń tylko SWOJE taby
    $myTabs = array(
        'AdminMojModulMain',
        'AdminMojModulSettings',
        // ... inne taby twojego modułu
    );

    foreach ($myTabs as $class) {
        $id = (int)Tab::getIdFromClassName($class);
        if ($id) {
            $tab = new Tab($id);
            $tab->delete();
        }
    }

    // Usuń Secret Sauce TYLKO jeśli nie ma innych dzieci
    $id_secret_sauce = (int)Tab::getIdFromClassName('AdminSecretSauce');
    if ($id_secret_sauce) {
        $children = Tab::getTabs(Context::getContext()->language->id, $id_secret_sauce);
        if (empty($children)) {
            $ss = new Tab($id_secret_sauce);
            $ss->delete();
        }
    }

    return true;
}
```

---

## Kluczowe zasady

1. **Secret Sauce bez właściciela**
   ```php
   $secretSauce->module = ''; // NIE przypisuj do żadnego modułu
   ```

2. **Sprawdzaj czy istnieje przed utworzeniem**
   ```php
   $secret_sauce_id = (int)Tab::getIdFromClassName('AdminSecretSauce');
   if (!$secret_sauce_id) {
       // Utwórz tylko jeśli nie istnieje
   }
   ```

3. **Jeden widoczny link na moduł**
   - Główny kontroler: `id_parent = $secret_sauce_id`
   - Pozostałe kontrolery: `id_parent = -1` (ukryte, dostępne przez kod)

4. **Przy odinstalowaniu - nie usuwaj Secret Sauce jeśli ma dzieci**
   ```php
   $children = Tab::getTabs(..., $id_secret_sauce);
   if (empty($children)) {
       // Usuń tylko gdy pusty
   }
   ```

---

## Struktura menu po dodaniu kilku modułów

```
IMPROVE
└── Secret Sauce (science icon)
    ├── WiseBlock        (moduł wiseblock)
    ├── Mój Moduł        (moduł mojmodul)
    ├── Inny Moduł       (moduł innymodul)
    └── ...
```

---

## Kontroler AdminSecretSauce

Jeśli tworzysz moduł jako pierwszy (Secret Sauce jeszcze nie istnieje), dodaj pusty kontroler który przekieruje do pierwszego dziecka:

`controllers/admin/AdminSecretSauceController.php`:
```php
<?php
class AdminSecretSauceController extends ModuleAdminController
{
    public function __construct()
    {
        parent::__construct();

        // Przekieruj do pierwszego dostępnego modułu
        // (opcjonalnie - można zostawić pustą stronę)
        $children = Tab::getTabs($this->context->language->id, $this->id);
        if (!empty($children)) {
            Tools::redirectAdmin($this->context->link->getAdminLink($children[0]['class_name']));
        }
    }
}
```

**Uwaga:** Kontroler AdminSecretSauceController jest już w module WiseBlock. Jeśli WiseBlock jest zainstalowany, nie musisz go dodawać w innych modułach.

---

## Checklist dla nowego modułu

- [ ] Skopiuj kod `installTabs()` i dostosuj nazwy klas/tabów
- [ ] Skopiuj kod `uninstallTabs()` i dostosuj listę swoich tabów
- [ ] Wywołaj `$this->installTabs()` w metodzie `install()`
- [ ] Wywołaj `$this->uninstallTabs()` w metodzie `uninstall()`
- [ ] Utwórz kontrolery admin dla każdego taba
- [ ] Przetestuj instalację gdy Secret Sauce już istnieje
- [ ] Przetestuj instalację gdy Secret Sauce nie istnieje
- [ ] Przetestuj odinstalowanie - czy Secret Sauce zostaje gdy są inne moduły
