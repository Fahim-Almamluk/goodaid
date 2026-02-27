# Helper script to start Laravel dev server as a background process and log output
# Run this script via Scheduled Task at system startup or manually

$php = 'C:\xampp\php\php.exe'
$cwd = 'C:\xampp\htdocs\goodaid'
$log = Join-Path $cwd 'storage\logs\artisan-server.log'

# Ensure log directory exists
if (-not (Test-Path -Path (Split-Path $log))) {
    New-Item -ItemType Directory -Path (Split-Path $log) -Force | Out-Null
}

# Start the server using cmd.exe so we can redirect output to a log file
$cmd = "cd /d $cwd && \"$php\" artisan serve --host=127.0.0.1 --port=8000 >> \"$log\" 2>&1"
Start-Process -FilePath 'cmd.exe' -ArgumentList "/c $cmd" -WindowStyle Hidden

# Optionally write a small entry to the log indicating the script ran
Add-Content -Path $log -Value "[" + (Get-Date).ToString('u') + "] Started artisan serve via Scheduled Task or script.`n"