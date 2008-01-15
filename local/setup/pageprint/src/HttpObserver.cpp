/*
 * An observer
 *
 */

#include "HttpObserver.h"
#include "Config.h"
#include <glib.h>
#include "nsIHttpChannel.h"
#include "nsStringAPI.h"
#include "nsEmbedString.h"
#include "nsIServiceManager.h"
#include "nsServiceManagerUtils.h"
#include "nsIObserverService.h"

HttpObserver::HttpObserver(void)
{
	muck = true;
}

HttpObserver::~HttpObserver()
{
}

NS_IMPL_ISUPPORTS1(HttpObserver, nsIObserver)

NS_IMETHODIMP
HttpObserver::Observe(nsISupports *aSubject, 
			const char *aTopic, 
			const PRUnichar *aData
			)
			     
{
	if (muck) {
		//g_print("Topic %s\n",aTopic);
		Config *ppConf = Config::Instance();

		nsresult rv;
		
		nsCOMPtr<nsIChannel> channel(do_QueryInterface((nsISupports*)aSubject,&rv)); 
		nsCOMPtr<nsIHttpChannel> httpChannel(do_QueryInterface(channel)); 

		//nsEmbedCString *header = new nsEmbedCString("X-Test");
		//nsEmbedCString *value = new nsEmbedCString("blah");

		//rv = httpChannel->SetRequestHeader(header,value,PR_FALSE);
		//rv = httpChannel->SetRequestHeader(nsDependentCString("X-TEST"),nsDependentCString("Test"),PR_FALSE);

		for(int i = 0; i < ppConf->requestHeaderNames->len; i++) {
			gchar *name = (gchar *)g_ptr_array_index(ppConf->requestHeaderNames,i);
			gchar *value = (gchar *)g_ptr_array_index(ppConf->requestHeaderValues,i);
			rv = httpChannel->SetRequestHeader(
						nsEmbedCString(name),
						nsEmbedCString(value),
						PR_FALSE
			);
//NS_CStringToUTF16(nsEmbedCString(" "), NS_CSTRING_ENCODING_UTF8, empty);
		}

		// removing segfaults 
		//observerService->RemoveObserver(this,"http-on-modify-request");

		muck = false;
	}

	return NS_OK;
}
void
HttpObserver::SetupToModifyHeaders () 
{
	nsresult rv;

	nsCOMPtr<nsIObserverService> observerService(do_GetService("@mozilla.org/observer-service;1", &rv)); 

	if (NS_SUCCEEDED(rv)) {
		rv = observerService->AddObserver(this,"http-on-modify-request",PR_FALSE);

		if (NS_FAILED(rv)) {
			g_print("Adding Observer Failed\n");
		}
	}
}
