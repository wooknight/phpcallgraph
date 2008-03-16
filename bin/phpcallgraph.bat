@echo off
rem Find the application home.
rem %~dp0 is location of current script under NT
set _REALPATH=%~dp0
php "%_REALPATH%phpcallgraph" "%*"
