#!/bin/bash

TMPDIR="${TMPDIR:-$TEMP}"
TMPDIR="${TMPDIR:-$TMP}"

CURRENT_DIR=`pwd`
TARGET_DIR="${CURRENT_DIR}/assets/google-material-symbols/"
DIR="${TMPDIR}google-material-symbols/"

echo "Installing Google Material Symbols"
echo "  Cloning repository to temporary directory:"
echo "    $DIR"

rm -rf $DIR
mkdir -p $DIR

cd $DIR && git clone --branch main --single-branch --depth 1 --filter=blob:none --no-checkout https://github.com/marella/material-symbols.git .  &>/dev/null
cd $DIR && git sparse-checkout init --cone  &>/dev/null
cd $DIR && git sparse-checkout set svg/300/outlined/  &>/dev/null
cd $DIR && git checkout  &>/dev/null

echo "  Moving icons to target directory:"
echo "    $TARGET_DIR"
rm -rf $TARGET_DIR
mkdir -p $TARGET_DIR
mv $DIR/svg/300/outlined/* $TARGET_DIR

echo "  Building assets/blocks/scripts/icon-names.js"
cd $CURRENT_DIR && node build-icons.js
