@echo off
setlocal enabledelayedexpansion

:: Get the current directory
set "SCRIPT_DIR=%~dp0"
set "SCRIPT_NAME=%~nx0"

echo ========================================
echo   Cleanup Dev Environment
echo ========================================
echo.
echo This will delete all files and folders except:
echo   - index.php
echo   - configs/
echo   - controllers/
echo   - icon/
echo   - repos/
echo   - views/
echo   - uploads/ (folder structure only)
echo.
echo Press Ctrl+C to cancel, or
pause

:: Navigate to script directory
cd /d "%SCRIPT_DIR%"

:: Delete all files in root except index.php and this script
for %%F in (*) do (
    if /i not "%%F"=="index.php" (
        if /i not "%%F"=="%SCRIPT_NAME%" (
            echo Deleting file: %%F
            del /f /q "%%F"
        )
    )
)

:: Delete all folders except the protected ones
for /D %%D in (*) do (
    set "FOLDER_NAME=%%D"
    set "PROTECTED=0"
    
    if /i "!FOLDER_NAME!"=="configs" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"=="controllers" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"=="icon" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"=="repos" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"=="views" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"=="uploads" set "PROTECTED=1"
    if /i "!FOLDER_NAME!"==".git" set "PROTECTED=1"
    
    if "!PROTECTED!"=="0" (
        echo Deleting folder: %%D
        rmdir /s /q "%%D"
    )
)

:: Clean uploads folder - keep categories, products, profiles subfolders, delete all their contents
if exist "uploads" (
    echo Cleaning uploads/ folder...
    for /D %%D in (uploads\*) do (
        set "UPLOAD_SUBFOLDER=%%~nxD"
        set "UPLOAD_PROTECTED=0"
        
        if /i "!UPLOAD_SUBFOLDER!"=="categories" set "UPLOAD_PROTECTED=1"
        if /i "!UPLOAD_SUBFOLDER!"=="products" set "UPLOAD_PROTECTED=1"
        if /i "!UPLOAD_SUBFOLDER!"=="profiles" set "UPLOAD_PROTECTED=1"
        
        if "!UPLOAD_PROTECTED!"=="0" (
            echo Deleting uploads/%%~nxD
            rmdir /s /q "%%D"
        ) else (
            echo Cleaning uploads/%%~nxD/ contents...
            for /D %%S in (uploads\%%~nxD\*) do (
                rmdir /s /q "%%S"
            )
            for %%F in (uploads\%%~nxD\*) do (
                del /f /q "%%F"
            )
        )
    )
    for %%F in (uploads\*) do (
        if exist "%%F" (
            echo Deleting uploads/%%F
            del /f /q "%%F"
        )
    )
)

echo.
echo ========================================
echo   Cleanup Complete!
echo ========================================
echo.
echo Deleting this script...
timeout /t 2 /nobreak >nul

:: Delete itself
del /f /q "%SCRIPT_DIR%%SCRIPT_NAME%"

endlocal
