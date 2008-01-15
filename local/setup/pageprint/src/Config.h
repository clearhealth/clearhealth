#ifndef __Config_h
#define __Config_h

#include <glib.h>

class Config
{
 public:
  Config();
  ~Config();
  static Config* Instance();
  void LoadFile(gchar* file);
  void PrintSnapSetup();
  void PrintPrintSetup();
  void setBrowserWidth(gchar* size);
  void setBrowserHeight(gchar* size);

  enum {MODE_SNAPSHOT, MODE_PDF, MODE_PRINT };
  int pagePrintMode;

  gchar *url;
  gchar *outputFile;

  int minWidth;
  int maxWidth;

  int minHeight;
  int maxHeight;

  int smallWidth;
  int smallHeight;

  int mediumWidth;
  int mediumHeight;

  int medium2Width;
  int medium2Height;

  int largeWidth;
  int largeHeight;

  bool outputFullSnap;

  int browserWidth;
  int browserHeight;

  gchar *printPaperType;
  double printMarginBottom;
  double printMarginTop;
  double printMarginLeft;
  double printMarginRight;
  bool printShrinkToFit;

  bool setAdditionalHeaders;

  // request header fiddling stuff
  GPtrArray *requestHeaderNames;
  GPtrArray *requestHeaderValues;
 private:
  static Config* pinstance;
};

#endif /* __Config_h */

