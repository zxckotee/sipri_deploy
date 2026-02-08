# Rebuild Instructions

The Apache ServerName warnings persist because the containers need to be **rebuilt**, not just restarted. The Apache configuration files are copied into the Docker image during the build process.

## Steps to Fix:

1. **Rebuild the containers:**
   ```bash
   cd /home/fsociety/sipri_deploy
   docker-compose down
   docker-compose build --no-cache
   docker-compose up -d
   ```

2. **Verify the fixes:**
   ```bash
   docker-compose logs -f
   ```
   
   You should no longer see the `AH00558: apache2: Could not reliably determine the server's fully qualified domain name` warnings.

3. **Check if the 500 error is resolved:**
   - Open `http://localhost/` (SIPRI) in your browser
   - Check the logs: `docker-compose logs sipri | tail -n 50`
   - If errors persist, check the CodeIgniter logs in `sipri/public_html/crm/writable/logs/`

## What Was Fixed:

1. ✅ Added `ServerName localhost` to both Apache config files
2. ✅ Updated SIPRI database config to use environment variables
3. ✅ Enabled error logging in CodeIgniter (threshold set to 4)
4. ✅ Fixed app.sipri database connection initialization

## Troubleshooting:

If you still see 500 errors after rebuilding:

1. Check database connection:
   ```bash
   docker-compose exec sipri php -r "echo getenv('SIPRI_DB_HOST');"
   ```

2. Check if database is accessible:
   ```bash
   docker-compose exec sipri php -r "\$conn = new mysqli(getenv('SIPRI_DB_HOST') ?: 'db', getenv('SIPRI_DB_USER') ?: 'sipri', getenv('SIPRI_DB_PASS') ?: 'sipri', getenv('SIPRI_DB_NAME') ?: 'sipri_crm'); echo \$conn->connect_error ?: 'Connected';"
   ```

3. Check CodeIgniter logs:
   ```bash
   cat sipri/public_html/crm/writable/logs/log-*.log | tail -n 50
   ```
