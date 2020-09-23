@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/league/commonmark/bin/commonmark
php "%BIN_TARGET%" %*
