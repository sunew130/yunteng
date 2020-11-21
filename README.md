#define UNICODE
#define _UNICODE
#include <windows.h>
#include <tchar.h>

BOOL GetCurrentUserAndDomain(PTSTR szUser, PDWORD pcchUser,
      PTSTR szDomain, PDWORD pcchDomain) {

   BOOL         fSuccess = FALSE;
   HANDLE       hToken   = NULL;
   PTOKEN_USER  ptiUser  = NULL;
   DWORD        cbti     = 0;
   SID_NAME_USE snu;

   __try {

      // Get the calling thread's access token.
      if (!OpenThreadToken(GetCurrentThread(), TOKEN_QUERY, TRUE,
            &hToken)) {

         if (GetLastError() != ERROR_NO_TOKEN)
            __leave;

         // Retry against process token if no thread token exists.
         if (!OpenProcessToken(GetCurrentProcess(), TOKEN_QUERY,
               &hToken))
            __leave;
      }

      // Obtain the size of the user information in the token.
      if (GetTokenInformation(hToken, TokenUser, NULL, 0, &cbti)) {

         // Call should have failed due to zero-length buffer.
         __leave;

      } else {

         // Call should have failed due to zero-length buffer.
         if (GetLastError() != ERROR_INSUFFICIENT_BUFFER)
            __leave;
      }

      // Allocate buffer for user information in the token.
      ptiUser = (PTOKEN_USER) HeapAlloc(GetProcessHeap(), 0, cbti);
      if (!ptiUser)
         __leave;

      // Retrieve the user information from the token.
      if (!GetTokenInformation(hToken, TokenUser, ptiUser, cbti, &cbti))
         __leave;

      // Retrieve user name and domain name based on user's SID.
      if (!LookupAccountSid(NULL, ptiUser->User.Sid, szUser, pcchUser,
            szDomain, pcchDomain, &snu))
         __leave;

      fSuccess = TRUE;

   } __finally {

      // Free resources.
      if (hToken)
         CloseHandle(hToken);

      if (ptiUser)
         HeapFree(GetProcessHeap(), 0, ptiUser);
   }

   return fSuccess;
}

int main()
{
    TCHAR user[1024], domain[1024];
    DWORD chUser = sizeof(user), chDomain = sizeof(domain);
    if (GetCurrentUserAndDomain(user, &chUser, domain, &chDomain))
    {
        _tprintf(TEXT("user:%s\ndomain:%s\n"), user, domain);
    }
}
