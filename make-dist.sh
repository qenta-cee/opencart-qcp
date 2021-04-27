#!/bin/bash

set -e

rm -rf dist
mkdir -p dist/upload
cd dist
cp ../install.xml .
cp -r ../admin ../catalog ../system upload/
zip -9r qcp.ocmod.zip .

