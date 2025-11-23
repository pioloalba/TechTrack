# TechTrack Database Export Script (PowerShell)
Write-Host "================================================" -ForegroundColor Cyan
Write-Host "   TechTrack Database Export" -ForegroundColor Cyan
Write-Host "================================================" -ForegroundColor Cyan
Write-Host ""

$outputFile = "sql\techtrack_db_complete.sql"
$dbName = "techtrack_db"

# Try to find mysqldump in common WAMP locations
$possiblePaths = @(
    "C:\wamp64\bin\mysql\mysql8.3.0\bin\mysqldump.exe",
    "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqldump.exe",
    "C:\wamp64\bin\mysql\mysql5.7.39\bin\mysqldump.exe",
    "C:\xampp\mysql\bin\mysqldump.exe"
)

$mysqldumpPath = $null
foreach ($path in $possiblePaths) {
    if (Test-Path $path) {
        $mysqldumpPath = $path
        break
    }
}

if (-not $mysqldumpPath) {
    Write-Host "✗ mysqldump.exe not found in common locations" -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative: Export from phpMyAdmin" -ForegroundColor Yellow
    Write-Host "1. Open phpMyAdmin: http://localhost/phpmyadmin" -ForegroundColor White
    Write-Host "2. Select 'techtrack_db' database" -ForegroundColor White
    Write-Host "3. Click 'Export' tab" -ForegroundColor White
    Write-Host "4. Click 'Go' to download SQL file" -ForegroundColor White
    Write-Host "5. Save as: sql\techtrack_db_complete.sql" -ForegroundColor White
    exit 1
}

Write-Host "Using: $mysqldumpPath" -ForegroundColor Green
Write-Host "Exporting database: $dbName"
Write-Host "Output: $outputFile"
Write-Host ""

try {
    & $mysqldumpPath -uroot $dbName | Out-File -FilePath $outputFile -Encoding UTF8
    
    if (Test-Path $outputFile) {
        $fileSize = (Get-Item $outputFile).Length / 1KB
        Write-Host "================================================" -ForegroundColor Green
        Write-Host "   ✓ SUCCESS! Database exported" -ForegroundColor Green
        Write-Host "================================================" -ForegroundColor Green
        Write-Host ""
        Write-Host "File: $outputFile" -ForegroundColor White
        Write-Host "Size: $([math]::Round($fileSize, 2)) KB" -ForegroundColor White
        Write-Host ""
        Write-Host "Your groupmate can import with:" -ForegroundColor Yellow
        Write-Host "  mysql -uroot techtrack_db < sql\techtrack_db_complete.sql" -ForegroundColor White
    } else {
        Write-Host "✗ Export failed - file not created" -ForegroundColor Red
    }
} catch {
    Write-Host "✗ Error: $_" -ForegroundColor Red
}
