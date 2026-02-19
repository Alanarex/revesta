# Newsletters

This document explains how the newsletter scheduling and sending system works in this codebase and how to configure cron on the server to run it automatically.

## Code components

- Command registration
  - `routes/console.php` registers `newsletter:dispatch-scheduled` (or see `app/Console/Commands/DispatchScheduledNewsletters.php`).

- Service
  - `app/Services/NewsletterCampaignService.php`
    - `schedule($id, $scheduledAt)` marks a campaign as scheduled.
    - `getScheduled()` returns scheduled campaigns.
    - `sendNow($id)` dispatches `SendNewsletterCampaignJob` for a campaign.

- Job
  - `app/Jobs/SendNewsletterCampaignJob.php`
    - Loads verified subscribers in chunks, queues a `NewsletterCampaignMail` for each, writes `NewsletterCampaignLog` entries and uses the repository `markAsSent()` to mark campaign as sent.

- Repository
  - `app/Repositories/NewsletterCampaignRepository.php` has `markAsScheduled()` and `markAsSent()`.

- Mailable / view
  - `app/Mail/NewsletterCampaignMail.php`
  - `resources/views/emails/newsletter-campaign.blade.php`

## Step-by-step flow (what happens when you schedule)

1. Admin creates or edits a campaign and sets a `scheduled_at` datetime via the admin UI.
2. Controller (`NewsletterCampaignController::schedule`) validates and calls `NewsletterCampaignService::schedule()` which updates the campaign status to `SCHEDULED` and persists `scheduled_at`.
3. On the server, a cron job runs regularly and invokes the dispatch command (see "Cron configuration" below). The command finds scheduled campaigns whose `scheduled_at <= now()` and calls `sendNow()` for each.
4. `sendNow()` dispatches `SendNewsletterCampaignJob` (queued). The job loads verified subscribers (chunked), queues the per-recipient mailable, logs sends, and finally calls `markAsSent()` to update campaign status, `sent_at` and `sent_count`.
5. Queue workers process the queued mail jobs and the mails are delivered by your configured mail driver.

## Cron configuration (server)

 This project registers the `newsletter:dispatch-scheduled` Artisan command. It is now scheduled inside the application (see `routes/console.php`) so the recommended approach is to use Laravel's scheduler and add the single cron entry below. Running the dispatch command directly from cron still works if you prefer that model.

Recommended crontab line (deploy user):

```
* * * * * cd /var/www/revesta && /usr/bin/php /var/www/revesta/artisan schedule:run >> /var/log/revesta/scheduler-cron.log 2>&1
```

Notes:
- Use the absolute path to `php` and your project path.
- Create a log directory and set appropriate ownership before enabling cron:
  ```bash
  sudo mkdir -p /var/log/revesta
  sudo chown deployuser:www-data /var/log/revesta
  sudo chmod 755 /var/log/revesta
  ```
- To avoid overlapping runs (if dispatch can be long), use `flock`:
  ```cron
  * * * * * flock -n /var/lock/revesta-newsletter.lock /usr/bin/php /var/www/revesta/artisan newsletter:dispatch-scheduled >> /var/log/revesta/newsletter-cron.log 2>&1
  ```

## Queue workers

Make sure you have persistent queue workers (Supervisor or systemd) to process `SendNewsletterCampaignJob` and queued mail delivery jobs. Example Supervisor program:

```ini
[program:revesta-worker]
command=/usr/bin/php /var/www/revesta/artisan queue:work --sleep=3 --tries=3 --memory=512
user=www-data
numprocs=1
autostart=true
autorestart=true
redirect_stderr=true
stdout_logfile=/var/log/revesta/worker.log
```

## Verification & troubleshooting

- Check the cron-driven dispatch log:
  ```bash
  tail -n 200 /var/log/revesta/newsletter-cron.log
  ```
- Check Laravel runtime logs for exceptions: `storage/logs/laravel.log`.
- Confirm queue workers are running:
  ```bash
  ps aux | grep "artisan queue:work" | grep -v grep
  sudo supervisorctl status
  ```
- Check failed jobs:
  ```bash
  php artisan queue:failed
  ```

## Where to look in code

- `routes/console.php` — command registration
- `app/Console/Commands/DispatchScheduledNewsletters.php` — command (if present)
- `app/Services/NewsletterCampaignService.php` — scheduling / sendNow
- `app/Jobs/SendNewsletterCampaignJob.php` — chunked sending and logging
- `app/Repositories/NewsletterCampaignRepository.php` — markAsScheduled / markAsSent
- `app/Mail/NewsletterCampaignMail.php` and `resources/views/emails/newsletter-campaign.blade.php`

## Next steps (optional)

- Add monitoring for failed jobs and alerting.
- Add per-recipient retry/failure tracking to the job if you want more robust delivery guarantees.
- Add a small admin dashboard showing campaign progress (sent_count, last run time).

*Files created locally in the workspace only; no git commits were made.*

## Exact cron commands (copy/paste)

Create log directory and set permissions (run once):

```bash
sudo mkdir -p /var/log/revesta
sudo chown $USER:www-data /var/log/revesta
sudo chmod 755 /var/log/revesta
```

Append the cron line to the deploy user's crontab (safe append):

```bash
# as the deploy user (or the user you use for deployments)
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/revesta && /usr/bin/php /var/www/revesta/artisan schedule:run >> /var/log/revesta/scheduler-cron.log 2>&1") | crontab -
```

Or edit interactively:

```bash
crontab -e
# then paste the line below and save:
* * * * * cd /var/www/revesta && /usr/bin/php /var/www/revesta/artisan schedule:run >> /var/log/revesta/scheduler-cron.log 2>&1
```

Verify the crontab entry:

```bash
crontab -l
```

Check that cron runs the command and writes to the log (wait ~1 minute then):

```bash
tail -n 200 /var/log/revesta/newsletter-cron.log
# check system cron activity (Debian/Ubuntu):
sudo systemctl status cron
journalctl -u cron --since "1 hour ago" | tail -n 100
```

If you prefer to avoid overlapping dispatch runs, use `flock`:

```bash
(crontab -l 2>/dev/null; echo "* * * * * flock -n /var/lock/revesta-newsletter.lock /usr/bin/php /var/www/revesta/artisan newsletter:dispatch-scheduled >> /var/log/revesta/newsletter-cron.log 2>&1") | crontab -
```

## Supervisor (install & recommended config)

If you use Supervisor to manage queue workers, install and enable Supervisor, copy the example program from the repo, create logs, and start the worker. Below is a concise, copy/paste sequence (Debian/Ubuntu):

Recommended Supervisor program (already in the repo at `deploy/supervisor-revesta.conf`):

```ini
[program:revesta-worker]
process_name=%(program_name)s_%(process_num)02d
command=/usr/bin/php /var/www/revesta/artisan queue:work --sleep=3 --tries=3 --memory=512 --queue=default,emails
user=www-data
numprocs=1
autostart=true
autorestart=true
startsecs=5
redirect_stderr=true
stdout_logfile=/var/log/revesta/worker.log
stderr_logfile=/var/log/revesta/worker.err.log
```

Install, copy config, create logs and start Supervisor:

```bash
# Install Supervisor (if missing)
sudo apt update
sudo apt install -y supervisor

# Copy the example config into Supervisor
sudo cp /var/www/revesta/deploy/supervisor-revesta.conf /etc/supervisor/conf.d/revesta-worker.conf

# Create log files and set permissions (run once)
sudo mkdir -p /var/log/revesta
sudo touch /var/log/revesta/worker.log /var/log/revesta/worker.err.log
sudo chown -R www-data:www-data /var/log/revesta
sudo chmod 750 /var/log/revesta

# Enable + start Supervisor service and load the program
sudo systemctl enable --now supervisor
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start revesta-worker:*

# Check status and follow logs
sudo supervisorctl status
tail -f /var/log/revesta/worker.log /var/log/revesta/worker.err.log
```

Restart workers after deploy so they pick up new code:

```bash
# gracefully restart Laravel queue workers
php /var/www/revesta/artisan queue:restart
# or via Supervisor
sudo supervisorctl restart revesta-worker:*
```

Troubleshooting quick checks:

```bash
ps aux | grep 'artisan queue:work' | grep -v grep
php /var/www/revesta/artisan queue:failed
```

Replace `www-data` with your deploy user if needed.

Optional: run the scheduler under Supervisor instead of cron

If you prefer not to add a cron entry for `schedule:run`, you can run the scheduler as a long-running process with Supervisor. Example program:

```ini
[program:revesta-scheduler]
command=/usr/bin/php /var/www/revesta/artisan schedule:work
user=www-data
autostart=true
autorestart=true
stdout_logfile=/var/log/revesta/scheduler.log
stderr_logfile=/var/log/revesta/scheduler.err.log
```

If you use `withoutOverlapping()` in your schedule definitions, ensure your cache driver supports locks (redis, memcached, or database) so the scheduler's locking works correctly.

## Manual testing & verification steps

1. Trigger the dispatch immediately (manual):

```bash
cd /var/www/revesta
/usr/bin/php artisan newsletter:dispatch-scheduled
```

2. Inspect Laravel logs for exceptions or errors:

```bash
tail -n 200 storage/logs/laravel.log
grep -i error storage/logs/laravel.log | tail -n 50
```

3. Verify the job was dispatched and processed (ensure workers are running):

```bash
php artisan queue:failed   # check failed jobs
ps aux | grep 'artisan queue:work' | grep -v grep
sudo supervisorctl status
```

4. If mails are queued but not delivered, confirm `QUEUE_CONNECTION` and mail driver are configured in `.env` and that workers are running as the correct user.

## Optional improvements

- Add monitoring/alerts for failed jobs and job backlog.
- Add a small admin dashboard showing last dispatch time, sent_count, and errors.
- Add per-recipient failure logs and retry bookkeeping in the job for greater reliability.

