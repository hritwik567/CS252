#!/bin/bash

ap=" "
while :; do
  cnt=""
  for (( i=0; i<100; i++ )); do
    cat train | awk -v i="$cnt" '{print i $0 }'
    cnt="$ap$cnt"
    sleep 0.5
    clear
  done
done
