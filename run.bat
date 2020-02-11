@echo off 
 
rem 进入当前盘符
%~d0
 
rem 进入当前所在路径
cd %~dp0

php artisan serve