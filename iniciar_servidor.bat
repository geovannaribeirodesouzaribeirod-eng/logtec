@echo off
title LogTec - Servidor Localhost
echo ========================================================
echo   Iniciando Servidor Localhost do LogTec (Porta 8000)
echo ========================================================
echo.
start "" "http://localhost:8000/"
powershell -ExecutionPolicy Bypass -File "%~dp0server.ps1" -Port 8000
pause
