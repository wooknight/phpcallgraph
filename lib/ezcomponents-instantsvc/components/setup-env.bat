@echo off

rem cd trunk

dir /a:d | find "autoload" > nul
if errorlevel=1 mkdir autoload

for /D %%I in (*.) do if exist %%I\src\*_autoload.php copy %%I\src\*_autoload.php autoload\