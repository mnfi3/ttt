@ECHO OFF
setlocal DISABLEDELAYEDEXPANSION
SET BIN_TARGET=%~dp0/../vendor/jonnyw/php-phantomjs/bin/phantomjs
php "%BIN_TARGET%" %*
