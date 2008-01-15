/*
 */

#ifndef __HttpObserver_h
#define __HttpObserver_h

#include "nsIObserver.h"
#include "nsWeakReference.h"
#include "nsIServiceManager.h"
#include "nsIObserverService.h"
#include <glib.h>

class HttpObserver : public nsIObserver
{
 public:
  HttpObserver();
  virtual ~HttpObserver();
  void HttpObserver::SetupToModifyHeaders();

  NS_DECL_ISUPPORTS
  NS_DECL_NSIOBSERVER 

 private:
  nsCOMPtr<nsIObserverService> observerService;
  bool muck;
};

#endif /* __HttpObserver_h */
