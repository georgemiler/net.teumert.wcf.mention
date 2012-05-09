# name of the package, built file will be named <packageName>.tar
packageName:=$(shell basename $(CURDIR))

# choose from templates.tar, acptemplates.tar, files.tar
objects = files.tar

# build complete package
all: .submodules $(objects) twitter-text-php.tar
	tar --concatenate --file=files.tar twitter-text-php.tar
	tar cvf $(packageName).tar * --exclude files --exclude twitter-text-php --exclude twitter-text-php.tar

# pseudo-dependency to fetch submodules
.submodules:
	git submodule init && git submodule update 

# files	pip
files.tar:
	cd files && tar cvf ../files.tar * && cd ..
	
# acptemplates pip
acptemplates.tar:
	cd acptemplates && tar cvf ../acptemplates.tar * && cd ..
	
# templates pip
templates.tar:
	cd templates && tar cvf ../templates.tar * && cd ..

# twitter-text-php 
twitter-text-php.tar:	
	cd twitter-text-php && tar cvf ../twitter-text-php.tar lib/ && cd ..
	
# Remove the already packed files
.PHONY : clean
clean :
	rm *.tar
# End of makefile 
