#!/usr/bin/env bash

rm -f all.min.js

#echo "///// jquery"      >> all.min.js
cat jquery-1.10.2.min.js >> all.min.js
echo ""                  >> all.min.js

#echo "///// raphael"     >> all.min.js
cat raphael-2.1.0.min.js >> all.min.js
echo ""                  >> all.min.js

#echo "///// jquery.qtip" >> all.min.js
cat jquery.qtip.min.js   >> all.min.js
echo ""                  >> all.min.js

#echo "///// chroma"      >> all.min.js
cat chroma.min.js        >> all.min.js
echo ""                  >> all.min.js

#echo "///// kartograph"  >> all.min.js
cat kartograph.min.js    >> all.min.js
echo ""                  >> all.min.js

#echo "///// script"      >> all.min.js
uglifyjs script.js       -o script.min.js
cat script.min.js        >> all.min.js
echo ""                  >> all.min.js

rm -f script.min.js
rm -f *.min.min.js

