/*
 */

#ifndef __PrintProgress_h
#define __PrintProgress_h

#include <nsIWebProgressListener.h>
#include <nsWeakReference.h>
#include <glib.h>

class PrintProgress : public nsIWebProgressListener
{
 public:
  PrintProgress();
  PrintProgress(gchar *psFile, gchar *pdfFile);
  virtual ~PrintProgress();

  NS_DECL_ISUPPORTS

  NS_DECL_NSIWEBPROGRESSLISTENER

  char *mPsFile;
  char *mPdfFile;
 private:

  void generatePDF (gchar *ps, gchar *pdf);

};

#endif /* __PrintProgress_h */
