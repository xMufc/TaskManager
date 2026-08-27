# TaskManager

Aplikacja do zarządzania zadaniami i asynchronicznego importu CSV, przygotowana w Laravel 13, React 18, TypeScript i Inertia. Środowisko uruchomieniowe zapewnia Laravel Sail (PHP 8.5, MySQL 8.4, Redis), a kolejkę importów obsługuje Laravel Horizon.

## Wymagania

- Docker z pluginem Docker Compose
- Git

PHP, Composer, Node i bazy danych działają w kontenerach, więc nie trzeba instalować ich globalnie.

## Uruchomienie

```bash
cp .env.example .env
```

Wartości `DB_*` w `.env` domyślnie pasują do kontenerów Sail i nie trzeba ich zmieniać, chyba że istnieje potrzeba użycią innych danych.

```bash
docker run --rm -u "$(id -u):$(id -g)" -v "$PWD":/app -w /app composer:2 composer install --ignore-platform-req=ext-pcntl
./vendor/bin/sail up -d --build
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
./vendor/bin/sail npm install
./vendor/bin/sail npm run build
```

Aplikacja jest dostępna pod `http://localhost`. Dane logowania po migracji z seederem: `test@example.com` / `password` (albo zarejestruj nowe konto przez `/register`).

### Uruchomienie Horizon (kolejki, import CSV)

W osobnym terminalu, zostaw uruchomione w tle:

```bash
./vendor/bin/sail artisan horizon
```

Panel Horizon jest dostępny pod `http://localhost/horizon`. Stan usług można sprawdzić poleceniem:

```bash
./vendor/bin/sail ps
```

Podczas pracy nad frontendem zamiast produkcyjnego buildu można uruchomić:

```bash
./vendor/bin/sail npm run dev
```

## Testy i jakość

Wszystkie testy backendu uruchamia jedna komenda:

```bash
./vendor/bin/sail test
```

Frontend i typy weryfikuje:

```bash
./vendor/bin/sail npm run build
```

Testy obejmują czystą logikę domenową, wszystkie dozwolone i odrzucone przejścia statusów, CRUD i izolację danych użytkowników, filtrowanie, import poprawny/niepoprawny/częściowo udany oraz wyniki polecenia CLI.

## Import CSV

Plik musi być zapisany jako CSV z separatorem średnikowym, wszystko umieszczone w cudzysłowiach i dokładnie takimi nagłówkami:

```bash
"title";"description";"priority";"due_date"
```

- `title` — wymagany, maks. 255 znaków
- `description` — opcjonalny
- `due_date` — opcjonalna data w formacie `YYYY-MM-DD`
- `priority` — `low`, `medium`, `high` albo `urgent`

Przykład znajduje się w [`storage/app/examples/tasks.csv`](examples/tasks-import-example.csv). Po wysłaniu pliku powstaje rekord importu, a proces trafia do kolejki Redis. Horizon przetwarza każdy wiersz niezależnie. Błędny wiersz nie wycofuje poprawnych: raport pokazuje numer wiersza, przekazane dane, wynik i dokładny powód odrzucenia. Widok wyniku odświeża dane bez przeładowania całej strony.

## Przepływ statusów

- Każde ręcznie tworzone zadanie zaczyna jako `todo`, dzięki czemu nie można ominąć procesu.
- `todo -> in_progress`: praca faktycznie się rozpoczęła.
- `in_progress -> blocked`: pojawiła się przeszkoda; `blocked -> in_progress` oznacza jej usunięcie.
- `in_progress -> done`: zadanie można zakończyć tylko po rozpoczęciu pracy.
- `done` jest stanem końcowym. Cofnięcie wymagałoby osobnej decyzji biznesowej (np. zdarzenia „reopen”), dlatego nie jest ukryte w zwykłej zmianie statusu.
- `todo -> cancelled`, `in_progress -> cancelled`, `blocked -> cancelled`: zadanie może przejść w końcową fazę anulowania, gdy postanowiono porzucić dane zadania. Cofnięcie wymagałoby osobnej decyzji biznesowej (np. zdarzenia „reopen”), dlatego nie jest ukryte w zwykłej zmianie statusu.

Reguły egzekwuje niezależnie od frameworka. Niepoprawne przejście zgłasza typowany błąd domenowy `InvalidTaskStatusTransitionException`.

## Polecenie CLI

Usunięcie zadań utworzonych dawniej niż podana liczba dni:

```bash
./vendor/bin/sail artisan tasks:prune 30
```

Komenda odrzuca wartości ujemne i wypisuje dokładną liczbę usuniętych rekordów. Operacja również przechodzi przez Command i dedykowany Handler.

## Architektura i decyzje

Kod zapisujący dane stosuje CQRS: kontrolery i Job tworzą obiekty z `app/Application/**/Commands`, a każda komenda ma jeden Handler. Odczyt przechodzi przez `Queries` i `QueryHandlers`. Rozdział jest celowy: import, HTTP i CLI korzystają z tych samych przypadków użycia bez powielania reguł, a kontrolery ograniczają się do transportu.

Warstwa `app/Domain/Task` zawiera logikę domenową: statusy, priorytety, modele, wyjątki i zdarzenia. Eloquent, transakcje i integracja z Laravel są obsługiwane na zewnątrz domeny, w warstwie aplikacyjnej i infrastrukturze. `TaskData` z `spatie/laravel-data` stanowi typowaną granicę między zwalidowanym wejściem a komendami.

Istotne działania emitują zdarzenia `TaskCreated`, `TaskDeleted`, `TaskStatusChanged`, `TaskUpdated`, `TasksImported` i `TasksPruned`. Listener zapisuje je strukturalnie do logu.

Import CSV jest odporny na retry. Wcześniej przetworzone wiersze są pomijane. Błędy danych dotyczą pojedynczych wierszy, a błędy struktury pliku są raportowane jako odrzucony wynik importu.

Frontend jest ułożony według Atomic Design w `resources/js/Components`: `Atoms`, `Molecules`, `Organisms`. Hooki:

- `useTaskFilter` zarządza filtrem i częściową nawigacją Inertia bez przeładowania strony.
- `useTaskStatusChange` zarządza zmianą statusu zadania z optymistyczną aktualizacją interfejsu oraz obsługą błędów i stanu oczekiwania bez przeładowania strony.
- `useImportPooling` monitoruje status importu poprzez cykliczne odświeżanie danych Inertia bez przeładowania strony i zatrzymuje odpytywanie po zakończeniu importu.
