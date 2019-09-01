#!/bin/bash

if [ -f tmp/$1.pdf ]
then
    echo "TRUE"
else
    echo "FALSE"
fi
