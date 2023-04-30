@echo off

set zip_file=intercalc.zip

rem Delete the existing zip file
if exist %zip_file% del %zip_file%

rem Create the new zip file of the src folder
cd src
compact /c /s ..\%zip_file% *.*
cd ..