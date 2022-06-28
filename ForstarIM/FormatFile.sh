tr "\t" " " < $1 > $1.temp
tr " *<" " <" < $1.temp > $2
rm $1.temp
