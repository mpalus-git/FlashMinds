# ⚡ FlashMinds

**FlashMinds** to webowa aplikacja do nauki z wykorzystaniem fiszek. Umożliwia tworzenie talii kart, zarządzanie nimi oraz efektywną naukę dzięki algorytmowi powtórek rozłożonych w czasie.

---

## 📑 Spis treści

- [Video](#-video)
- [Funkcjonalności](#-funkcjonalności)
- [Technologie](#-technologie)
- [System Leitnera](#-system-leitnera)
- [Struktura projektu](#-struktura-projektu)
- [Wymagania](#-wymagania)
- [Instalacja](#-instalacja)
- [Konfiguracja](#-konfiguracja)
- [Uruchomienie](#-uruchomienie)
- [Bezpieczeństwo](#-bezpieczeństwo)

---

## 🎬 Video

Poniżej znajduje się film prezentujący działanie aplikacji FlashMinds wraz z omówieniem wszystkich funkcjonalności:

https://github.com/user-attachments/assets/010ccde6-3a0c-458b-8b39-7304ecab924e

---

## ✨ Funkcjonalności

| Funkcja | Opis |
|---|---|
| **Dashboard** | Przegląd wszystkich talii z paskiem postępu i liczbą kart do powtórki |
| **Tworzenie talii** | Dodawanie nowych talii z nazwą i opcjonalnym opisem |
| **Zarządzanie kartami** | Dodawanie, edycja i usuwanie fiszek w ramach talii |
| **Tryb nauki** | Interaktywna nauka z animacją odwracania karty |
| **System Leitnera** | Automatyczne planowanie powtórek na podstawie 5-pudełkowego systemu |
| **Śledzenie postępów** | Wizualny pasek postępu i statystyki dla każdej talii |
| **Responsywność** | Interfejs dostosowujący się do urządzeń mobilnych i desktopowych |

---

## 🛠 Technologie

| Warstwa | Technologia |
|---|---|
| Backend | PHP 8+ |
| Baza danych | MySQL / MariaDB |
| Łączność z bazą | PDO (prepared statements) |
| Frontend | HTML5, CSS3 |
| Czcionka | Inter (Google Fonts) |
| Architektura | MVC-like (widoki + akcje) |

---

## 🧠 System Leitnera

Aplikacja implementuje 5-pudełkowy system Leitnera - sprawdzoną metodę powtórek rozłożonych w czasie:

```
Pudełko 1 → powtórka co 1 dzień
Pudełko 2 → powtórka co 2 dni
Pudełko 3 → powtórka co 5 dni
Pudełko 4 → powtórka co 8 dni
Pudełko 5 → powtórka co 14 dni (opanowane ✅)
```

- **Poprawna odpowiedź** → karta przechodzi do wyższego pudełka
- **Błędna odpowiedź** → karta wraca do pudełka 1

---

## 📂 Struktura projektu

```
FlashMinds/
├── index.php              # Dashboard - lista talii
├── deck.php               # Edytor talii - zarządzanie kartami
├── study.php              # Tryb nauki - sesja powtórkowa
├── header.php             # Nagłówek i nawigacja
├── footer.php             # Stopka strony
├── config.php             # Konfiguracja bazy danych i funkcje pomocnicze
├── css/
│   └── style.css          # Stylowanie całej aplikacji
├── sql/
│   └── schema.sql         # Schemat bazy danych
├── actions/
│   ├── deck_create.php    # Tworzenie nowej talii
│   ├── deck_delete.php    # Usuwanie talii
│   ├── card_create.php    # Dodawanie karty
│   ├── card_update.php    # Edycja karty
│   ├── card_delete.php    # Usuwanie karty
│   └── study_answer.php   # Obsługa odpowiedzi w trybie nauki
└── README.md
```

---

## 📋 Wymagania

- **PHP** 8.0 lub nowszy
- **MySQL** 5.7+ / **MariaDB** 10.3+
- Serwer HTTP (Apache, Nginx lub wbudowany serwer PHP)
- Rozszerzenie PHP `pdo_mysql`

---

## 🚀 Instalacja

1. **Sklonuj repozytorium:**

   ```bash
   git clone https://github.com/mpalus-git/FlashMinds.git
   cd FlashMinds
   ```

2. **Utwórz bazę danych:**

   Zaimportuj schemat z pliku `sql/schema.sql`:

   ```bash
   mysql -u root -p < sql/schema.sql
   ```

   Schemat automatycznie utworzy bazę `flashminds` oraz tabele `decks` i `cards`.

3. **Skonfiguruj połączenie z bazą** (szczegóły w sekcji [Konfiguracja](#-konfiguracja)).

4. **Uruchom aplikację** (szczegóły w sekcji [Uruchomienie](#-uruchomienie)).

---

## ⚙️ Konfiguracja

Plik `config.php` zawiera ustawienia połączenia z bazą danych:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'flashminds');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
```


---

## ▶️ Uruchomienie

Najprostszy sposób to wbudowany serwer PHP:

```bash
php -S localhost:8000
```

Następnie otwórz w przeglądarce:

```
http://localhost:8000
```

Alternatywnie umieść pliki projektu w katalogu serwera Apache/Nginx (np. `htdocs` lub `www`).

---

## 🔒 Bezpieczeństwo

Aplikacja stosuje następujące środki bezpieczeństwa:

- **Ochrona CSRF** - tokeny generowane per sesja i weryfikowane przy każdym formularzu
- **Prepared Statements (PDO)** - zabezpieczenie przed SQL Injection
- **Escapowanie wyjścia** - funkcja `htmlspecialchars()` chroni przed XSS
- **Tryb wyjątków PDO** - błędy bazy danych są obsługiwane przez wyjątki

---

<div align="center">
  <sub>⚡FlashMinds</sub>
</div>
