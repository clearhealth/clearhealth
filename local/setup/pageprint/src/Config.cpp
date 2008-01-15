/**
 * A class for holding PagePrint config information
 */

#include "Config.h"

#include <libxml/xmlreader.h>
#include <stdlib.h>

Config* Config::pinstance = 0;// initialize pointer
Config* Config::Instance () 
{
	if (pinstance == 0)  // is it the first call?
	{  
		pinstance = new Config(); // create sole instance
	}
	return pinstance; // address of sole instance
}

// setup default config
Config::Config(void)
{
	minWidth=640;
	maxWidth=1024;

	minHeight=480;
	maxHeight=786;

	smallWidth=80;
	smallHeight=60;

	mediumWidth=160;
	mediumHeight=120;

	medium2Width=320;
	medium2Height=240;

	largeWidth=640;
	largeHeight=480;

	browserWidth=1024;
	browserHeight=768;

	outputFullSnap = true;

	printPaperType = g_strdup("Letter");
	printMarginBottom = 0.5;
	printMarginTop = 0.5;
	printMarginLeft = 0.5;
	printMarginRight = 0.5;

	printShrinkToFit = false;

	setAdditionalHeaders = true;

	pagePrintMode = Config::MODE_PDF;

	url = g_strdup("http://bluga.net/");
	outputFile = g_strdup("/tmp/test.pdf");

	requestHeaderNames = g_ptr_array_new();
	requestHeaderValues = g_ptr_array_new();

	//lprCmd = g_strdup("/usr/bin/lpr");
}
Config::~Config() {
	g_free(printPaperType);

	g_free(url);
	g_free(outputFile);

	//g_free(lprCmd);

	g_ptr_array_free(requestHeaderNames,TRUE);
	g_ptr_array_free(requestHeaderValues,TRUE);
}
void
Config::PrintSnapSetup() {
	g_print("Configuration:\n minWidth: %i\n maxWidth: %i\n minHeight: %i\n maxHeight: %i\n smallWidth: %i\n smallHeight: %i\n mediumWidth: %i\n mediumHeight: %i\n medium2Width: %i\n medium2Height: %i\n largeWidth: %i\n largeHeight: %i\n browserWidth: %i\n browserHeight: %i\n outputFullSnap: %i\n\n",
		minWidth,
		maxWidth,
		minHeight,
		maxHeight,
		smallWidth,
		smallHeight,
		mediumWidth,
		mediumHeight,
		medium2Width,
		medium2Height,
		largeWidth,
		largeHeight,
		browserWidth,
		browserHeight,
		outputFullSnap);
}
void
Config::PrintPrintSetup() {
	g_print("Configuration:\n PaperType: %s\n MarginBottom: %f\n MarginTop: %f\n MarginLeft: %f\n MarginRight: %f\n ShrinkToFit: %d\n\n",
		printPaperType, printMarginBottom, printMarginTop, printMarginLeft, printMarginRight, printShrinkToFit);
}

void
Config::setBrowserWidth(gchar *size) {
	browserWidth = atoi(size);
	if (browserWidth > maxWidth) {
		browserWidth = maxWidth;
	}
	if (browserWidth < minWidth) {
		browserWidth = minWidth;
	}
}
void
Config::setBrowserHeight(gchar *size) {
	browserHeight = atoi(size);
	if (browserHeight > maxHeight) {
		browserHeight = maxHeight;
	}
	if (browserHeight < minHeight) {
		browserHeight = minHeight;
	}
}
/*
void 
Config::setLprCmd(gchar* cmd) {
	lprCmd = cmd;
}
*/

void
Config::LoadFile(gchar* file) {
	xmlTextReaderPtr reader;
	int ret;
	bool set = false;

	reader = xmlReaderForFile(file, NULL, 0);
	if (reader != NULL) {
		ret = xmlTextReaderRead(reader);

		const xmlChar *name, *value;
		gchar *n, *v;
		while (ret == 1) {
			//processNode(reader);

			switch(xmlTextReaderNodeType(reader)) {
				case XML_READER_TYPE_ELEMENT:
					name = xmlTextReaderConstName(reader);
				break;
				case XML_READER_TYPE_TEXT:
					value = xmlTextReaderConstValue(reader);
					set = true;
				break;
			}

			if (set) { 
				v = (gchar *) value;
				n = (gchar *) name;
				if (g_ascii_strcasecmp(n,"minWidth") == 0) {
					minWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"maxWidth") == 0) {
					maxWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"minHeight") == 0) {
					minHeight = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"maxHeight") == 0) {
					maxHeight = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"smallWidth") == 0) {
					smallWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"smallHeight") == 0) {
					smallHeight = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"mediumWidth") == 0) {
					mediumWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"mediumHeight") == 0) {
					mediumHeight = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"medium2Width") == 0) {
					medium2Width = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"medium2Height") == 0) {
					medium2Height = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"largeWidth") == 0) {
					largeWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"largeHeight") == 0) {
					largeHeight = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"browserWidth") == 0) {
					browserWidth = atoi(v);
				}
				if (g_ascii_strcasecmp(n,"browserHeight") == 0) {
					browserHeight = atoi(v);
				}

				if (g_ascii_strcasecmp(n,"outputFullSnap") == 0) {
					outputFullSnap = false;
                                        if (atoi(v)) {
						outputFullSnap = true;
					}
                                }

				if (g_ascii_strcasecmp(n,"shrinkToFit") == 0) {
					printShrinkToFit = false;
                                        if (atoi(v)) {
						printShrinkToFit = true;
					}
                                }

				if (g_ascii_strcasecmp(n,"paperType") == 0) {
					printPaperType = g_strdup_printf("%s",v);
				}

				if (g_ascii_strcasecmp(n,"marginTop") == 0) {
					printMarginTop = atof(v);
				}
				if (g_ascii_strcasecmp(n,"marginBottom") == 0) {
					printMarginBottom = atof(v);
				}
				if (g_ascii_strcasecmp(n,"marginLeft") == 0) {
					printMarginLeft = atof(v);
				}
				if (g_ascii_strcasecmp(n,"marginRight") == 0) {
					printMarginRight = atof(v);
				}
				set = false;
			}

			ret = xmlTextReaderRead(reader);
		}
		xmlFreeTextReader(reader);
		if (ret != 0) {
			g_print("%s : failed to parse\n", file);
		}
	} else {
		g_print("Unable to open %s\n", file);
	}
}
