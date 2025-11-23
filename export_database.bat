@echo off
echo ================================================
echo   TechTrack Database Export Script
echo ================================================
echo.

set OUTPUT_FILE=sql\techtrack_db_complete.sql
set DB_NAME=techtrack_db
set DB_USER=root
set DB_PASS=

echo Exporting database: %DB_NAME%
echo Output file: %OUTPUT_FILE%
echo.

mysqldump -u%DB_USER% %DB_NAME% > %OUTPUT_FILE%

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ================================================
    echo   SUCCESS! Database exported successfully
    echo ================================================
    echo.
    echo File location: %OUTPUT_FILE%
    echo.
    echo Share this file with your groupmate to import:
    echo   mysql -uroot techtrack_db ^< sql\techtrack_db_complete.sql
    echo.
) else (
    echo.
    echo ERROR: Database export failed
    echo Make sure MySQL is running and database exists
    echo.
)

pause
