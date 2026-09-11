param(
    [int]$Port = 8000
)

$projectRoot = Split-Path -Parent $PSScriptRoot
Set-Location $projectRoot

# This command does not run migrations, seeders, or any database command.
php artisan serve --host=127.0.0.1 --port=$Port
