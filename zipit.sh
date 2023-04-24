#!/bin/bash

# The name of the zip file to create
zip_file="intercalc.zip"

rm -rf $zip_file
# Create the zip file of the icalc folder
zip -r $zip_file icalc/