#!/usr/local/bin/fontforge
Open($2);
Print($fontname);
Generate($1+"/fonts/"+$2:r+".ttf");
Generate($1+"/fonts/"+$2:r+".eot");
Generate($1+"/fonts/"+$2:r+".woff");
Generate($1+"/fonts/"+$2:r+".svg");
Quit(0);