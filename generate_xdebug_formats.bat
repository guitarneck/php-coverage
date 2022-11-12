@echo off & setlocal
set all=export serialize json dot dump coverage coveralls lcov clover raw
for %%a in (%all%) do @php bin\coverage tests\fixtures\tests\Hello.test.php --handler=xdebug --includes=tests/fixtures/tests/src/ --format=%%a