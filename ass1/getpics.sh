#!/bin/bash

rm ./apod/*.jpg ./apod/*.png ./apod/*.html ./apod/*.txt
cd ./apod
wget https://www.cse.iitk.ac.in/users/namanv/pics.html
a='https://www.cse.iitk.ac.in/users/namanv'
grep 'src="' pics.html > temp.txt
b=$(awk -F "\"" '{print $2}' temp.txt)
c=$(gshuf -n 1 -e $b)
c=${c:1:${#c}-1}
d="$a$c"
echo "$d"
wget $d
c=${c:12:${#c}-12}
c=`pwd`/$c
osascript -e 'tell application "Finder" to set desktop picture to "'"$c"'" as POSIX file'
cd ~/Desktop/CS252/ass1
