# أناقة ستور — Hairdresser Loyalty System

This is a small Laravel app to manage hairdressers, sales and a loyalty card system.

Key features

-   Add hairdressers and track sales
-   Calculate and store loyalty points per hairdresser
-   Assign cards to hairdressers; card release and expiration dates are managed

---

## Quick setup (Windows / XAMPP)

1. Copy `.env.example` to `.env` and update DB credentials.
2. Install PHP dependencies:

```powershell
composer install
```

3. Install Node dependencies and build assets (optional for dev):

```powershell
npm install
npm run dev
```

4. Generate app key and run migrations (if you prefer migrations over SQL dumps):

```powershell
php artisan key:generate
php artisan migrate
php artisan db:seed
```

5. Start the local dev server:

```powershell
php artisan serve
```

Open http://127.0.0.1:8000 and log in with seeded users (if present).

---

## Database notes

-   The project includes SQL dumps in `database/`:

    -   `hair.sql` — development dump
    -   `loyalty-system.sql` — alternative dump

-   Cards behavior (Release & Expiration):
    -   When a card is added, `Release_Date` should start from the time it is added and `Expiration_Date` should be exactly 1 year later.
    -   Because some import tools (phpMyAdmin) don't accept `DELIMITER` blocks used to create triggers, the trigger creation is commented-out in the SQL dumps.
    -   To create the trigger manually (mysql CLI), run:

```sql
DELIMITER //
CREATE TRIGGER `cards_before_insert` BEFORE INSERT ON `cards`
FOR EACH ROW
BEGIN
	IF NEW.Release_Date IS NULL THEN
		SET NEW.Release_Date = CURRENT_TIMESTAMP;
	END IF;
	SET NEW.Expiration_Date = DATE_ADD(NEW.Release_Date, INTERVAL 1 YEAR);
END;//
DELIMITER ;
```

After creating the trigger you can insert cards without dates and the DB will set `Release_Date = NOW()` and `Expiration_Date = Release_Date + 1 YEAR`.

If you prefer to avoid triggers, the application can set these dates in Laravel when creating cards — tell me and I can add that migration/controller logic.

---

## How to import the SQL dump (phpMyAdmin)

1. Open phpMyAdmin and select your database.
2. Import `database/hair.sql`. The trigger is commented out to avoid import errors.
3. If you need the trigger, use the mysql CLI to run the `DELIMITER` block shown above.

---

## Development notes

-   Tailwind CSS is used for styling. Vite is configured in `vite.config.js` and the main assets are in `resources/css` and `resources/js`.
-   A searchable Tom Select dropdown was added to `resources/views/calculating-points.blade.php` for selecting hairdressers (no jQuery dependency).

---

## Next steps I can help with

-   Convert DB trigger + update into a Laravel migration (best practice).
-   Implement trigger-free behavior in Laravel: set Release_Date and Expiration_Date in the controller/model on card creation.
-   Add AJAX-backed searchable select (server-side search) if you expect many hairdressers.

---

If you want any of the next steps implemented, tell me which and I'll add the migration or code changes.
