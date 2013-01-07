#!/bin/bash

git merge -s ours --no-commit master
git rm -rf demo/
git read-tree --prefix=demo/ -u master
git commit -am "autoupdate"
