BASEDIR = .

INCLUDES = `pkg-config --cflags mozilla-gtkmozembed gtk+-2.0 mozilla-xpcom libxml-2.0` -I/usr/include/mozilla-1.7.13/gfx/ -I/usr/include/mozilla-seamonkey-1.0.5/gfx/

LIBS = `pkg-config --libs mozilla-xpcom mozilla-gtkmozembed gtk+-2.0 libxml-2.0` -lxpcom_core

CC = g++
CCFLAGS = $(INCLUDES) ${LIBS}

all: Config PrintProgress HttpObserver PagePrint Link

PrintProgress: PrintProgress.cpp
	${CC} -c -o PrintProgress.o PrintProgress.cpp ${INCLUDES}

Config: Config.cpp
	${CC} -c -o Config.o Config.cpp ${INCLUDES}

HttpObserver: HttpObserver.cpp
	${CC} -c -o HttpObserver.o HttpObserver.cpp ${INCLUDES}

PagePrint: PagePrint.cpp
	${CC} -c -o PagePrint.o PagePrint.cpp ${INCLUDES}

Link: PagePrint.o PrintProgress.o Config.o HttpObserver.o
	${CC} -o PagePrint-bin PagePrint.o PrintProgress.o Config.o HttpObserver.o ${LIBS}

install: PagePrint-bin PagePrint
	cp PagePrint-bin ../
	cp PagePrint ../
