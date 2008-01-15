// todo: input postscript output file on the command line
// todo: either wrap this thing in a way to be used multiple times or figure out when printing is done so we can close it
// todo: make hiding of page headers and footers optional
// todo: write up an install guide

// system stuff
#include <gtk/gtk.h>
#include <glib.h>
#include <glib/gprintf.h>
#include <gdk-pixbuf/gdk-pixbuf.h>
#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>
#include <fcntl.h>
#include <pwd.h>

// mozilla headers
#include "prenv.h"
#include "gtkmozembed.h"
#include "gtkmozembed_internal.h"
#include "nsXPCOM.h"

// Mozilla Printing Stuff
#include "nsIPrintSettings.h"
#include "nsIWebBrowserPrint.h"
#include "nsIWebBrowser.h"
#include "nsCOMPtr.h"
#include "nsIInterfaceRequestorUtils.h"
#include "nsEmbedString.h"

// print progress code
#include "PrintProgress.h"
#include "HttpObserver.h"

// config code
#include "Config.h"

// command line option parsing
#include "SimpleOpt.h"

// global variable
int firstOpen = 0;
GtkWidget *window;
GtkWidget *browser;
int progressCount = 0;
int lastProgressCount = 0;
int noProgress = 12;

GArray headerKeys;
GArray headerValues;


Config *ppConf = Config::Instance();

// snapshot webpage
bool
snapShotContents(GtkMozEmbed *m)
{
	g_print("Attempting to Snapshot Webpage\n");

	GdkWindow* gdkWin;
	GdkDrawable* drawable;
	GdkPixbuf*  buf;
	GdkPixbuf*  thumbBuf;

	GError *error = NULL;
	gboolean result;

	gdkWin = gtk_widget_get_parent_window(browser);

	drawable = GDK_DRAWABLE(gdkWin);

	//buf = gdk_pixbuf_new(GDK_COLORSPACE_RGB, FALSE, 16, 800, 600);
	buf = gdk_pixbuf_get_from_drawable	(
		NULL,
		drawable,
		gdk_colormap_get_system(),
		0,
		0,
		0,
		0,
		ppConf->browserWidth,
		ppConf->browserHeight
	);

	gchar* file;
	for(int i = 0; i < 5; i++) {
		switch(i) {
			case 0:// full snap
				if (!ppConf->outputFullSnap) {
					continue;
				}
				file = g_strdup_printf("%s.jpg",ppConf->outputFile);
				thumbBuf = gdk_pixbuf_scale_simple(buf,ppConf->browserWidth,ppConf->browserHeight,GDK_INTERP_BILINEAR);
			break;
			case 1:// small thumb
				thumbBuf = gdk_pixbuf_scale_simple(buf,ppConf->smallWidth,ppConf->smallHeight,GDK_INTERP_BILINEAR);
				file = g_strdup_printf("%s-thumb_small.jpg",ppConf->outputFile);
			break;
			case 2:// medium1 thumb
				thumbBuf = gdk_pixbuf_scale_simple(buf,ppConf->mediumWidth,ppConf->mediumHeight,GDK_INTERP_BILINEAR);
				file = g_strdup_printf("%s-thumb_medium.jpg",ppConf->outputFile);
			break;
			case 3:// medium2 thumb
				thumbBuf = gdk_pixbuf_scale_simple(buf,ppConf->medium2Width,ppConf->medium2Height,GDK_INTERP_BILINEAR);
				file = g_strdup_printf("%s-thumb_medium2.jpg",ppConf->outputFile);
			break;
			case 4:// large thumb
				thumbBuf = gdk_pixbuf_scale_simple(buf,ppConf->largeWidth,ppConf->largeHeight,GDK_INTERP_BILINEAR);
				file = g_strdup_printf("%s-thumb_large.jpg",ppConf->outputFile);
			break;
		}

		result = gdk_pixbuf_save(thumbBuf,file,"jpeg",&error,"quality", "75", NULL);
		if (result) {
			g_print("Successfully wrote snapshot: %s\n",file);
		}
		else {
			g_print("Error saving snapshot: %s\n",error->message);
		}
		g_free(file);
	}

	exit(0);
	return FALSE;
}

bool loadCheck(GtkMozEmbed *m)
{
	//g_print("Load Check: %d\n",progressCount);
	if (lastProgressCount == progressCount) {
		noProgress--;
		//g_print("No Progress: %d\n",noProgress);
	}
	lastProgressCount = progressCount;

	if (noProgress == 0) {
		g_print("Load Hung: snapshotting anyway\n");
		snapShotContents(m);
		return false;
	}
	return true;
}


// printing function
bool
printContents(GtkMozEmbed *m)
{
	g_print("Attempting to Print\n");

	nsresult result = FALSE;

	nsCOMPtr<nsIPrintSettings> settings;
     	nsCOMPtr<nsIWebBrowser> mWebBrowser;   

	//gtk_moz_embed_get_nsIWebBrowser (GTK_MOZ_EMBED(browser), getter_AddRefs(mWebBrowser));
	gtk_moz_embed_get_nsIWebBrowser (m, getter_AddRefs(mWebBrowser));
     	if (!mWebBrowser) {
		g_print("Failed to get mWebBrowser\n");
		exit(1);
	}

	//nsCOMPtr<nsIWebBrowserPrint> print(do_GetInterface(mWebBrowser, &result));
	//nsCOMPtr<nsIWebBrowserPrint> print(do_GetInterface(mWebBrowser, &result));
	nsCOMPtr<nsIWebBrowserPrint> print(do_GetInterface(mWebBrowser, &result));
     	if (NS_FAILED(result)) {
		g_print("Failed to get nsIWebBrowserPrint\n");
		exit(1);
	}

     	result = print->GetGlobalPrintSettings(getter_AddRefs(settings));
     	if (NS_FAILED(result)) {
		g_print("Failed to get GlobalPrintSettings\n");
		exit(1);
	}

	nsEmbedString mPrintToFile, mPaperName;

	gchar* printFile = g_strdup_printf("/tmp/PrintPage%d.ps",getpid());

	NS_CStringToUTF16(nsEmbedCString(printFile), NS_CSTRING_ENCODING_UTF8, mPrintToFile);
	NS_CStringToUTF16(nsEmbedCString(ppConf->printPaperType), NS_CSTRING_ENCODING_UTF8, mPaperName);

	settings->SetToFileName (mPrintToFile.get());

	settings->SetPrintToFile (PR_TRUE);

	settings->SetPaperName (mPaperName.get());

	settings->SetPrintSilent (PR_TRUE);

	settings->SetShowPrintProgress(PR_FALSE);

	settings->SetPrintBGImages(PR_TRUE);
	settings->SetPrintBGColors(PR_TRUE);
	settings->SetPrintInColor(PR_TRUE);

	if (ppConf->printShrinkToFit) {
		settings->SetShrinkToFit(PR_TRUE);
	}
	else {
		settings->SetShrinkToFit(PR_FALSE);
	}

	settings->SetMarginBottom(ppConf->printMarginBottom);
	settings->SetMarginTop(ppConf->printMarginTop);
	settings->SetMarginLeft(ppConf->printMarginLeft);
	settings->SetMarginRight(ppConf->printMarginRight);

	nsEmbedString empty;
	NS_CStringToUTF16(nsEmbedCString(" "), NS_CSTRING_ENCODING_UTF8, empty);
	
	settings->SetFooterStrLeft(empty.get());
	settings->SetFooterStrRight(empty.get());
	settings->SetHeaderStrLeft(empty.get());
	settings->SetHeaderStrRight(empty.get());

	PrintProgress *listener = new PrintProgress(printFile,ppConf->outputFile);
	result = print->Print(settings, listener);

	g_free(printFile);

	if (result != NS_OK) {
		g_print("Im pretty sure our print failed\n");
	}

	//g_print("Printing Result %d\n",NS_SUCCEEDED (result) ? TRUE : FALSE);

	return FALSE;
}


// browser loading progress callback
static void
on_progress(GtkMozEmbed *embed, gint cur, gint max, gpointer data)
{
	progressCount++;
	g_print("Progress: %d of %d\n",cur,max);
}

static void
on_start(GtkMozEmbed *embed, gpointer data)
{
	g_print("Document Load Started\n");
}

static void
on_stop(GtkMozEmbed *embed, gpointer data)
{
	g_print("Document Load Stopped\n");

	if (ppConf->pagePrintMode == Config::MODE_SNAPSHOT) {
		g_timeout_add(2500,GSourceFunc(snapShotContents),embed);
	}
	else {
		g_timeout_add(1000,GSourceFunc(printContents),embed);
	}
}

void ShowUsage() {
	g_print("Usage: PagePrint [-s] [-c file] [-?] [--help] URL OUTPUTFile\n");
}

int
main(int argc, char **argv)
{
	// load base config file if it exits
	ppConf->LoadFile("/etc/PagePrint.xml");

	// load command line arguments
	// http://www.codeproject.com/useritems/SimpleOpt.asp
	enum { OPT_HELP, OPT_SNAPSHOT, OPT_CONFIG, OPT_WIDTH, OPT_HEIGHT, OPT_HEADERNAME, OPT_HEADERVALUE, OPT_PRINT, OPT_LPR, OPT_IGNORE };
	CSimpleOpt::SOption g_rgOptions[] = {
	// for hackish print support in the print wrapper
	    { OPT_PRINT,	"-p",		SO_REQ_SEP	},
	    { OPT_PRINT,	"-o",		SO_REQ_SEP	},
	    { OPT_PRINT,	"-print",	SO_NONE		},
	// normal options
	    { OPT_PRINT,	"--print",	SO_NONE		},
	    { OPT_LPR,		"-l",		SO_REQ_SEP	},
	    { OPT_LPR,		"--lpr",	SO_REQ_SEP	},
	    { OPT_SNAPSHOT,	"-s",     	SO_NONE    	}, // "-s"
	    { OPT_SNAPSHOT,	"--snapshot",	SO_NONE    	}, // "--snapshot"
	    { OPT_CONFIG,	"-c",     	SO_REQ_SEP	}, // "-c config_file"
	    { OPT_CONFIG,	"--config",    	SO_REQ_SEP	}, // "--config=config_file"
	    { OPT_WIDTH,	"-w",     	SO_REQ_SEP	}, // 
	    { OPT_WIDTH,	"--width",    	SO_REQ_SEP	}, //
	    { OPT_HEIGHT,	"-h",     	SO_REQ_SEP	}, // 
	    { OPT_HEIGHT,	"--height",    	SO_REQ_SEP	}, //
	    { OPT_HEADERNAME,	"-hn",    	SO_REQ_SEP	}, //
	    { OPT_HEADERVALUE,	"-hv",    	SO_REQ_SEP	}, //
	    { OPT_HELP, 	"-?",     	SO_NONE		}, // "-?"
	    { OPT_HELP,		"--help", 	SO_NONE		}, // "--help"
	    { OPT_IGNORE,	"-i", 		SO_REQ_SEP	}, // "--ignore=randomstuff"
	    SO_END_OF_OPTIONS                       	      	   // END
	};
	CSimpleOpt args(argc, argv, g_rgOptions);

	while (args.Next()) {
		if (args.LastError() == SO_SUCCESS) {
			switch (args.OptionId()) {
				case OPT_HELP:
					ShowUsage();
					return 0;
					break;
				case OPT_SNAPSHOT:
					ppConf->pagePrintMode = Config::MODE_SNAPSHOT;
					break;
				case OPT_PRINT:
					ppConf->pagePrintMode = Config::MODE_PRINT;
					break;
				case OPT_LPR:
					//ppConf->setLprCmd(args.OptionArg());
					break;
				case OPT_CONFIG:
					ppConf->LoadFile(args.OptionArg());
					break;
				case OPT_WIDTH:
					ppConf->setBrowserWidth(args.OptionArg());
					break;
				case OPT_HEIGHT:
					ppConf->setBrowserHeight(args.OptionArg());
					break;
				case OPT_HEADERNAME:
					g_ptr_array_add(ppConf->requestHeaderNames,(gpointer)g_strdup_printf("%s",args.OptionArg()));
					break;
				case OPT_HEADERVALUE:
					g_ptr_array_add(ppConf->requestHeaderValues,(gpointer)g_strdup_printf("%s",args.OptionArg()));
					break;
			}
		/*
			g_print("Option, ID: %d, Text: '%s', Argument: '%s'\n", args.OptionId(), args.OptionText(), 
				args.OptionArg() ? args.OptionArg() : "");
		*/
		}
		else {
			g_print("Invalid argument: %s\n"), args.OptionText();
			return 1;
		}
	}

	//g_print("File Count: %i\n",args.FileCount());
	if (args.FileCount() < 2) {
		ShowUsage();
		return 1;
	}

	ppConf->url = g_strdup_printf("%s",args.File(0));
	ppConf->outputFile = g_strdup_printf("%s",args.File(1));

	g_print("Url: %s, Output: %s\n",ppConf->url,ppConf->outputFile);
	
	// Setup GTK and Mozilla environment
	g_print("Starting PagePrint\n");

	gtk_set_locale();
	gtk_init(&argc, &argv);

	// setup mozilla environment
	struct passwd *pass;
	char *full_path;
	
	if (pass = getpwuid(getuid())) {
		full_path = g_strdup_printf("%s/%s", pass->pw_dir, ".PagePrint");
	} else {
		g_print("getpwuid failed!");
		return 1;
	}
	
	gtk_moz_embed_set_profile_path(full_path, "PagePrint");
	g_print("Profile path is: %s\n",full_path);

	// get the size of the window we want to create
	int width = ppConf->browserWidth+16;
	int height = ppConf->browserHeight;

	// create a window and add the browser to it
	window = gtk_window_new(GTK_WINDOW_TOPLEVEL);
	gtk_window_set_title(GTK_WINDOW (window), "PagePrint");
	gtk_container_set_border_width (GTK_CONTAINER (window), 0);
	gtk_window_set_default_size(GTK_WINDOW(window), height+20, width+20);

	browser = gtk_moz_embed_new();
	gtk_container_add(GTK_CONTAINER (window),browser);
	gtk_widget_set_usize(browser, width, height);

	// connect to browser events
	g_signal_connect(G_OBJECT(browser), "progress",
                     G_CALLBACK(on_progress), browser);

	g_signal_connect(G_OBJECT(browser), "net_start",
                     G_CALLBACK(on_start), browser);

	g_signal_connect(G_OBJECT(browser), "net_stop",
                     G_CALLBACK(on_stop), browser);

	// setup listener for modifying headers if that is enabled in config
	if (ppConf->setAdditionalHeaders) {
		//g_print("Setting Additional Headers\n");
		HttpObserver *observer = new HttpObserver(); 
		observer->SetupToModifyHeaders();
	}	

	g_print("Loading URL: %s \n\n",ppConf->url);
	gtk_moz_embed_load_url(GTK_MOZ_EMBED(browser), ppConf->url);

	switch(ppConf->pagePrintMode) {
		case Config::MODE_PRINT:
		case Config::MODE_PDF:
			ppConf->PrintPrintSetup();
			break;
		case Config::MODE_SNAPSHOT:
			g_timeout_add(1000,GSourceFunc(loadCheck),GTK_MOZ_EMBED(browser));
			ppConf->PrintSnapSetup();
			break;
	}

	gtk_widget_show_all(window);


	gtk_main();

	return 0;
}
