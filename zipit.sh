#!/bin/bash

# The name of the zip file to create
zip_file="intercalc.zip"

rm -rf $zip_file
# Create the zip file of the src folder
zip -r $zip_file src/