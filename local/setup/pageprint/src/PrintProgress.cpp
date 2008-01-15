/*
 * A print progress listener, galeon-1.2.14 src/mozilla/PrintProgressListener.cpp and gtkmozembed EmbedProgress.cpp used as reference
 *
 */

#include "PrintProgress.h"

#include <glib.h>
#include <glib/gprintf.h>

#include <stdio.h>
#include <sys/stat.h>
#include <sys/types.h>
#include <stdlib.h>
#include <string.h>
#include <unistd.h>


PrintProgress::PrintProgress(void)
{
	mPsFile = NULL;
	mPdfFile = NULL;
}

PrintProgress::PrintProgress(gchar *psFile, gchar *pdfFile)
{
	PrintProgress ();
	mPsFile = psFile ? g_strdup (psFile) : NULL;
	mPdfFile = pdfFile ? g_strdup (pdfFile) : NULL;
}

PrintProgress::~PrintProgress()
{
	g_free (mPsFile);
	g_free (mPdfFile);
}

NS_IMPL_ISUPPORTS1(PrintProgress, nsIWebProgressListener)

NS_IMETHODIMP
PrintProgress::OnStateChange(nsIWebProgress *aWebProgress,
			     nsIRequest     *aRequest,
			     PRUint32        aStateFlags,
			     nsresult        aStatus)
{
	if (aStateFlags & nsIWebProgressListener::STATE_STOP)
	{
		// printing is done generate a pdf
		g_print("Print Complete\n");
		PrintProgress::generatePDF(mPsFile,mPdfFile);
	}
  return NS_OK;
}

NS_IMETHODIMP
PrintProgress::OnProgressChange(nsIWebProgress *aWebProgress,
				nsIRequest     *aRequest,
				PRInt32         aCurSelfProgress,
				PRInt32         aMaxSelfProgress,
				PRInt32         aCurTotalProgress,
				PRInt32         aMaxTotalProgress)
{
  return NS_OK;
}

NS_IMETHODIMP
PrintProgress::OnLocationChange(nsIWebProgress *aWebProgress,
				nsIRequest     *aRequest,
				nsIURI         *aLocation)
{
  return NS_OK;
}

NS_IMETHODIMP
PrintProgress::OnStatusChange(nsIWebProgress  *aWebProgress,
			      nsIRequest      *aRequest,
			      nsresult         aStatus,
			      const PRUnichar *aMessage)
{
  return NS_OK;
}

NS_IMETHODIMP
PrintProgress::OnSecurityChange(nsIWebProgress *aWebProgress,
				nsIRequest     *aRequest,
				PRUint32         aState)
{
  return NS_OK;
}

/*
int lastSize = 0;
bool writeCheck(PrintProgress *pp)
{
	struct stat fileInfo;
	int r = stat(pp->mPsFile,&fileInfo);

	if (fileInfo.st_size > lastSize) {
		lastSize = fileInfo.st_size;
		g_print("Size: %d\n",fileInfo.st_size);
		return true;
	}

	g_print("Time to try printing\n");

	return false;
}
*/

/* static */
void
PrintProgress::generatePDF(gchar *ps, gchar *pdf)
{
	g_print("ps file: %s\n",ps);
        //g_print("ps file: %s\n",ps);
	//g_print("We have a postscript file of: %s and we want to make a pdf named: %s\n",this);

	//g_timeout_add(500,GSourceFunc(writeCheck),g_strdup(ps),g_strdup(pdf));
	//g_print("Test\n");
	//exit(0);
}

/*
class Files
{
	Public:
	  PrintProgress();
	  PrintProgress(gchar *psFile, gchar *pdfFile);
	  ~PrintProgress();
	Private:
		gchar *ps;
		gchar *pdf;
}
Files::PrintProgress() {
	ps = g_strdup("");
	pdf = g_strdup("");
}
Files::PrintProgress(gchar *psFile, gchar *pdfFile) {
}
*/
