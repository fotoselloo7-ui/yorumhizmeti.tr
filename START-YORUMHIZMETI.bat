@echo off
setlocal
title YorumHizmeti.tr - Kontrol ve Local Sunucu
cd /d "%~dp0"

where php >nul 2>&1
if errorlevel 1 (
  echo.
  echo [HATA] PHP komutu bulunamadi.
  echo PHP kurulumunu veya PATH ayarini kontrol edin.
  echo.
  pause
  exit /b 1
)

echo [1/2] PHP dosyalari kontrol ediliyor...
for /r "well-known" %%F in (*.php) do (
  php -l "%%F" >nul 2>&1
  if errorlevel 1 (
    echo.
    echo [PHP HATASI] %%F
    php -l "%%F"
    echo.
    pause
    exit /b 1
  )
)

echo [2/2] Kontrol tamam. YorumHizmeti.tr baslatiliyor...
cd /d "%~dp0well-known"
start "" "http://127.0.0.1:8006"
php -S 127.0.0.1:8006 -t public
