package: files.tar twitter-text-php.tar
	tar --concatenate --file=files.tar twitter-text-php.tar
	tar cvf net.teumert.wcf.mention.tar * --exclude files --exclude twitter-text-php

files.tar:
	cd files && tar cvf ../files.tar * && cd ..

twitter-text-php.tar:
	git submodule init && git submodule update 
	cd twitter-text-php && tar cvf ../twitter-text-php.tar lib/ && cd ..
