@echo off
title YorumHizmeti.tr Local
cd /d "%~dp0well-known"
start "" "http://127.0.0.1:8006"
php -S 127.0.0.1:8006 -t public
