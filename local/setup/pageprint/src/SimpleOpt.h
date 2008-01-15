// File:    SimpleOpt.h
// Library: SimpleOpt
// Author:  Brodie Thiesfield <simpleopt@jellycan.com>
// Source:  http://code.jellycan.com/simpleopt/
// Version: 2.2
//
// INTRODUCTION
// ============
// This is a public domain multi-platform command line library which can parse
// almost any of the standard command line formats in use today. It is
// designed explicitly to be portable to any platform and has been tested on
// Windows and Linux. It also includes a cross-platform implementation of
// glob() so that wildcards in command line arguments are simply expanded for
// use by the program.
//
// FEATURES
// ========
//  * public domain == free to use in all software (including GPL and commercial)
//  * multi-platform (Windows 95/98/ME/NT/2K/XP, Linux, Unix)
//  * supports all lengths of option names:
//          -           switch character only (e.g. use stdin for input)
//          -o          short (single character)
//          -long       long (multiple character, single switch character)
//          --longer    long (multiple character, multiple switch characters)
//  * supports all types of arguments for options:
//          --option        short/long option flag (no argument)
//          --option ARG    short/long option with separate required argument
//          --option=ARG    short/long option with combined required argument
//          --option[=ARG]  short/long option with combined optional argument
//          -oARG           short option with combined required argument
//          -o[ARG]         short option with combined optional argument
//  * supports options which do not use a switch character. i.e. a special word
//    which is construed as an option. e.g. "foo.exe open /directory/file.txt"
//  * supports clumping of multiple short options (no arguments) in a string, e.g.
//      "foo.exe -abcdef file1" <==> "foo.exe -a -b -c -d -e -f file1"
//  * automatic recognition of a single slash as equivalent to a single hyphen
//    on Windows, e.g. "/f FILE" is equivalent to "-f FILE".
//  * file arguments can appear anywhere in the argument list:
//    "foo.exe file1.txt -a ARG file2.txt --flag file3.txt file4.txt"
//  * files will be returned to the application in the same order they were supplied
//    on the command line
//  * short-circuit option matching: "--man" will match "--mandate"
//  * invalid options can be handled while continuing to parse the command line
//  * valid options list can be changed dynamically during command line processing,
//    i.e. accept different options depending on an option supplied earlier in the
//    command line.
//  * implemented with only a single C++ header file
//  * uses no C runtime or OS functions
//  * char, wchar_t and Windows TCHAR in the same program
//  * complete working examples included
//  * compiles cleanly at warning level 4 (Windows/VC.NET 2003), warning level
//    3 (Windows/VC6) and -Wall (Linux/gcc)
//
// USAGE SUMMARY
// =============
// The SimpleOpt class is used by following these steps:
//
//    1. Include the SimpleOpt.h header file
//
//       #include "SimpleOpt.h"
//
//    2. Define an array of valid options for your program. 
//
//       CSimpleOpt::SOption g_rgOptions[] = {
//           { OPT_FLAG, _T("-a"),     SO_NONE    }, // "-a"
//           { OPT_FLAG, _T("-b"),     SO_NONE    }, // "-b"
//           { OPT_ARG,  _T("-f"),     SO_REQ_SEP }, // "-f ARG"
//           { OPT_HELP, _T("-?"),     SO_NONE    }, // "-?"
//           { OPT_HELP, _T("--help"), SO_NONE    }, // "--help"
//           SO_END_OF_OPTIONS                       // END
//       };
//
//       Note that all options must start with a hyphen even if the slash will
//       be accepted. This is because the slash character is automatically 
//       converted into a hyphen to test against the list of options. For example,
//       the following line matches both "-?" and "/?" (on Windows).
//
//           { OPT_HELP, _T("-?"),     SO_NONE    }, // "-?"
//
//    3. Instantiate a CSimpleOpt object supplying argc, argv and the option table
//
//       CSimpleOpt args(argc, argv, g_rgOptions);
//
//    4. Process the arguments by calling Next() until it returns false. On each call,
//       first check for an error by calling LastError(), then either handle the error
//       or process the argument.
//
//       while (args.Next()) {
//           if (args.LastError() == SO_SUCCESS) {
//               // handle option, using OptionId(), OptionText() and OptionArg()
//           }
//           else {
//               // handle error, one of: SO_OPT_INVALID, SO_OPT_MULTIPLE,
//               // SO_ARG_INVALID, SO_ARG_INVALID_TYPE, SO_ARG_MISSING
//           }
//       }
//
//    5. Process all non-option arguments with File(), Files() and FileCount()
//
//       ShowFiles(args.FileCount(), args.Files());
//
// NOTES
// =====
// * In MBCS mode, this library is guaranteed to work correctly only when all
//   option names use only ASCII characters.
//
// PUBLIC DOMAIN LICENCE
// =====================
// The author or authors of this code dedicate any and all copyright interest
// in this code to the public domain. We make this dedication for the benefit
// of the public at large and to the detriment of our heirs and successors. We
// intend this dedication to be an overt act of relinquishment in perpetuity
// of all present and future rights this code under copyright law.
//
// In short, you can do with it whatever you like: use it, modify it,
// distribute it, sell it, delete it, or send it to your mother-in-law.  I
// make no promises or guarantees that this code will work correctly or at all.
// Use it completely at your own risk.

#ifndef INCLUDED_SimpleOpt
#define INCLUDED_SimpleOpt


//! Error values
typedef enum _ESOError
{
    SO_SUCCESS          =  0,   //!< no error
    SO_OPT_INVALID      = -1,   //!< valid option format but not registered in the option table
    SO_OPT_MULTIPLE     = -2,   //!< multiple options matched the supplied option text
    SO_ARG_INVALID      = -3,   //!< argument was supplied but is not valid for this option
    SO_ARG_INVALID_TYPE = -4,   //!< argument was supplied in wrong format for this option
    SO_ARG_MISSING      = -5    //!< required argument was not supplied
} ESOError;

//! Option flags
enum _ESOFlags
{
    SO_O_EXACT    = 0x0001,    /**< Disallow partial matching of option names */
    SO_O_NOSLASH  = 0x0002,    /**< Disallow use of slash as an option marker on Windows.
                                    Un*x only ever recognizes a hyphen. */
    SO_O_SHORTARG = 0x0004,    /**< Permit arguments on single letter options with
                                    no equals sign. e.g. -oARG or -o[ARG] */
    SO_O_CLUMP    = 0x0008,    /**< Permit single character options to be clumped into
                                    a single option string. e.g. "-a -b -c" <==> "-abc" */
    SO_O_USEALL   = 0x0010,    /**< process the entire argv array for options,
                                    *including* the argv[0] entry. */
    SO_O_NOERR    = 0x0020     /**< do not generate an error for invalid options. errors 
                                    for missing arguments will still be generated. invalid
                                    options will be treated as files. invalid options in
                                    clumps will be silently ignored. */
};

/*! Types of arguments that options may have. Note that some of the _ESOFlags are
    not compatible with all argument types. SO_O_SHORTARG requires that relevant
    options use either SO_REQ_CMB or SO_OPT. SO_O_CLUMP requires that relevant options
    use only SO_NONE.
 */
typedef enum _ESOArgType {
    SO_NONE,    //!< No argument.                -o                 --opt
    SO_REQ_SEP, //!< Required separate argument. -o ARG             --opt ARG
    SO_REQ_CMB, //!< Required combined argument. -oARG    -o=ARG    --opt=ARG
    SO_OPT      //!< Optional combined argument. -o[ARG]  -o[=ARG]  --opt[=ARG]
} ESOArgType;

//! this option definition must be the last entry in the table
#define SO_END_OF_OPTIONS   { -1, 0, SO_NONE }

// use assertions to test the input data
#ifdef _DEBUG
# ifdef _WIN32
#  include <crtdbg.h>
#  define SO_ASSERT(b)    _ASSERTE(b)
# else
#  include <assert.h>
#  define SO_ASSERT(b)    assert(b)
# endif
#else
# define SO_ASSERT(b)
#endif

template<class SOCHAR>
class CSimpleOptTempl
{
public:
    struct SOption {
        int         nId;        //!< ID to return for this flag. Optional but must be >= 0
        SOCHAR *    pszArg;     //!< arg string to search for, e.g.  "open", "-", "-f", "--file"
                                // Note that on Windows the slash option marker will be converted
                                // to a hyphen so that "-f" will also match "/f".
        ESOArgType  nArgType;   //!< type of argument accepted by this option
    };

    CSimpleOptTempl() { Init(0, 0, 0, 0); }
    CSimpleOptTempl(int argc, SOCHAR * argv[], const SOption * a_rgOptions, int a_nFlags = 0) {
        Init(argc, argv, a_rgOptions, a_nFlags);
    }

    /*!
        Initialize the class in preparation for calling Next. The table of
        options pointed to by a_rgOptions does not need to be valid at the
        time that Init() is called. However on every call to Next() the
        table pointed to must be a valid options table with the last valid
        entry set to SO_END_OF_OPTIONS.

        NOTE: the array pointed to by a_argv will be modified by this
        class and must not be used or modified outside of member calls to
        this class.
    */
    void Init(int a_argc, SOCHAR * a_argv[], const SOption * a_rgOptions, int a_nFlags = 0) {
        m_argc           = a_argc;
        m_nLastArg       = a_argc;
        m_argv           = a_argv;
        m_rgOptions      = a_rgOptions;
        m_nLastError     = SO_SUCCESS;
        m_nOptionIdx     = 0;
        m_nOptionId      = -1;
        m_pszOptionText  = 0;
        m_pszOptionArg   = 0;
        m_nNextOption    = (a_nFlags & SO_O_USEALL) ? 0 : 1;
        m_szShort[0]     = (SOCHAR)'-';
        m_szShort[2]     = (SOCHAR)'\0';
        m_nFlags         = a_nFlags;
        m_pszClump       = 0;
    }

    /*!
        Call to advance to the next option. When all options have been processed
        it will return false. When true has been returned, you must check for an
        invalid or unrecognized option using the LastError() method. This will
        be return an error value other than SO_SUCCESS on an error. All standard
        data (e.g. OptionText(), OptionArg(), OptionId(), etc) will be available
        depending on the error.

        After all options have been processed, the remaining files from the
        command line can be processed in same order as they were passed to
        the program.

        Returns:
            true    option or error available for processing
            false   all options have been processed
    */
    bool Next() {
        // process a clumped option string if appropriate
        if (m_pszClump && *m_pszClump) {
            // silently discard invalid clumped option
            bool bIsValid = NextClumped();
            while (*m_pszClump && !bIsValid && (m_nFlags & SO_O_NOERR)) {
                bIsValid = NextClumped();
            }

            // return this option if valid or we are returning errors
            if (bIsValid || (m_nFlags & SO_O_NOERR) == 0) {
                return true;
            }
        }
        SO_ASSERT(!m_pszClump || !*m_pszClump);
        m_pszClump = 0;

        // init for the next option
        m_nOptionIdx    = m_nNextOption;
        m_nOptionId     = -1;
        m_pszOptionText = 0;
        m_pszOptionArg  = 0;
        m_nLastError    = SO_SUCCESS;

        // find the next option
        SOCHAR cFirst;
        int nTableIdx = -1;
        int nOptIdx = m_nOptionIdx;
        while (nTableIdx < 0 && nOptIdx < m_nLastArg) {
            // assumed argument
            m_pszOptionText = m_argv[nOptIdx];

            // find this option in the options table
            cFirst = PrepareArg(m_pszOptionText);
            if (m_pszOptionText[0] == (SOCHAR)'-') {
                // find any combined argument string and remove equals sign
                m_pszOptionArg = FindEquals(m_pszOptionText);
                if (m_pszOptionArg) {
                    *m_pszOptionArg++ = (SOCHAR)'\0';
                }
            }
            nTableIdx = LookupOption(m_pszOptionText);

            // if we didn't find this option and it is a short form
            // option then we try the alternative forms
            if (nTableIdx < 0
                && !m_pszOptionArg
                && m_pszOptionText[0] == (SOCHAR)'-'
                && m_pszOptionText[1]
                && m_pszOptionText[1] != (SOCHAR)'-'
                && m_pszOptionText[2])
            {
                // test for a short-form with argument if appropriate
                if (m_nFlags & SO_O_SHORTARG) {
                    m_szShort[1] = m_pszOptionText[1];
                    int nIdx = LookupOption(m_szShort);
                    if (nIdx >= 0
                        && (m_rgOptions[nIdx].nArgType == SO_REQ_CMB
                            || m_rgOptions[nIdx].nArgType == SO_OPT))
                    {
                        m_pszOptionArg  = &m_pszOptionText[2];
                        m_pszOptionText = m_szShort;
                        nTableIdx       = nIdx;
                    }
                }

                // test for a clumped short-form option string
                if (m_nFlags & SO_O_CLUMP)  {
                    m_pszClump = &m_pszOptionText[1];
                    ++m_nNextOption;
                    if (nOptIdx > m_nOptionIdx) {
                        ShuffleArg(m_nOptionIdx, nOptIdx - m_nOptionIdx);
                    }
                    return Next();
                }
            }

            // The option wasn't found. If it starts with a switch character
            // and we are not suppressing errors for invalid options then it
            // is reported as an error, otherwise it is data.
            if (nTableIdx < 0) {
                if ((m_nFlags & SO_O_NOERR) == 0
                    && m_pszOptionText[0] == (SOCHAR)'-') 
                {
                    break;
                }
                m_pszOptionText[0] = cFirst;
                ++nOptIdx;
                if (m_pszOptionArg) {
                    *(--m_pszOptionArg) = (SOCHAR)'=';
                }
            }
        }

        // end of options
        if (nOptIdx >= m_nLastArg) {
            if (nOptIdx > m_nOptionIdx) {
                ShuffleArg(m_nOptionIdx, nOptIdx - m_nOptionIdx);
            }
            return false;
        }
        ++m_nNextOption;

        // get the option id
        if (nTableIdx < 0) {
            m_nLastError = (ESOError) nTableIdx; // error code
        }
        else {
            m_nOptionId     = m_rgOptions[nTableIdx].nId;
            m_pszOptionText = m_rgOptions[nTableIdx].pszArg;

            // ensure that the arg type is valid
            ESOArgType nArgType = m_rgOptions[nTableIdx].nArgType;
            switch (nArgType) {
            case SO_NONE:
                if (m_pszOptionArg) {
                    m_nLastError = SO_ARG_INVALID;
                }
                break;

            case SO_REQ_SEP:
                if (m_pszOptionArg) {
                    m_nLastError = SO_ARG_INVALID_TYPE;
                }
                else if (nOptIdx+1 >= m_argc) {
                    m_nLastError = SO_ARG_MISSING;
                }
                else {
                    m_pszOptionArg = m_argv[nOptIdx+1];
                    ++m_nNextOption;
                }
                break;

            case SO_REQ_CMB:
                if (!m_pszOptionArg) {
                    m_nLastError = SO_ARG_MISSING;
                }
                break;

            case SO_OPT:
                // nothing to do
                break;
            }
        }

        // shuffle the files out of the way
        if (nOptIdx > m_nOptionIdx) {
            ShuffleArg(m_nOptionIdx, nOptIdx - m_nOptionIdx);
        }
        return true;
    }

    // access the details of the current option
    // NOTE: these functions are only valid after Next() returns true
    ESOError    LastError() const  { return m_nLastError; }
    int         OptionId() const   { return m_nOptionId; }
    SOCHAR *    OptionText() const { return m_pszOptionText; }
    SOCHAR *    OptionArg() const  { return m_pszOptionArg; }

    // access the files from the command line
    // NOTE: these functions are only valid after Next() returns false
    int         FileCount() const  { return m_argc - m_nLastArg; }
    SOCHAR *    File(int n) const  { return m_argv[m_nLastArg + n]; }
    SOCHAR **   Files() const      { return &m_argv[m_nLastArg]; }

private:
    SOCHAR PrepareArg(SOCHAR * pszString) const {
#ifdef _WIN32
        // On Windows we can accept the forward slash as a single character
        // option delimiter, but it cannot replace the '-' option used to
        // denote stdin. On Un*x paths may start with slash so it may not
        // be used to start an option.
        if ((m_nFlags & SO_O_NOSLASH) == 0
            && pszString[0] == (SOCHAR)'/'
            && pszString[1]
            && pszString[1] != (SOCHAR)'-')
        {
            pszString[0] = (SOCHAR) '-';
            return (SOCHAR)'/';
        }
#endif
        return pszString[0];
    }

    bool NextClumped()
    {
        // prepare for the next clumped option
        m_szShort[1]    = *m_pszClump++;
        m_nOptionId     = -1;
        m_pszOptionText = m_szShort;
        m_pszOptionArg  = 0;
        m_nLastError    = SO_SUCCESS;

        // lookup this option, ensure that we are using exact matching
        int nSavedFlags = m_nFlags;
        m_nFlags = SO_O_EXACT;
        int nTableIdx = LookupOption(m_pszOptionText);
        m_nFlags = nSavedFlags;

        // unknown option
        if (nTableIdx < 0) {
            m_nLastError = (ESOError) nTableIdx; // error code
            return false;
        }

        // valid option
        ESOArgType nArgType = m_rgOptions[nTableIdx].nArgType;
        if (nArgType == SO_NONE) {
            m_nOptionId = m_rgOptions[nTableIdx].nId;
            return true;
        }

        // invalid option as it requires an argument
        m_nLastError = SO_ARG_MISSING;
        return true;
    }

    // Shuffle arguments to the end of the argv array.
    //
    // For example:
    //      argv[] = { "0", "1", "2", "3", "4", "5", "6", "7", "8" };
    //
    //  ShuffleArg(1, 1) = { "0", "2", "3", "4", "5", "6", "7", "8", "1" };
    //  ShuffleArg(5, 2) = { "0", "1", "2", "3", "4", "7", "8", "5", "6" };
    //  ShuffleArg(2, 4) = { "0", "1", "6", "7", "8", "2", "3", "4", "5" };
    void ShuffleArg(int a_nStartIdx, int a_nCount) {
        SOCHAR * buf[200];
        int n, nSrc, nDst;
        int nTail = m_argc - a_nStartIdx - a_nCount;

        // make a copy of the elements to be moved
        nSrc = a_nStartIdx;
        nDst = 0;
        while (nDst < a_nCount) {
            buf[nDst++] = m_argv[nSrc++];
        }

        // move the tail down
        nSrc = a_nStartIdx + a_nCount;
        nDst = a_nStartIdx;
        for (n = 0; n < nTail; ++n) {
            m_argv[nDst++] = m_argv[nSrc++];
        }

        // append the moved elements to the tail
        nSrc = 0;
        nDst = a_nStartIdx + nTail;
        for (n = 0; n < a_nCount; ++n) {
            m_argv[nDst++] = buf[nSrc++];
        }

        // update the index of the last unshuffled arg
        m_nLastArg -= a_nCount;
    }

    // match on the long format strings. partial matches will be
    // accepted only if that feature is enabled.
    int LookupOption(const SOCHAR * a_pszOption) const {
        int nBestMatch = -1;    // index of best match so far
        int nBestMatchLen = 0;  // matching characters of best match
        int nLastMatchLen = 0;  // matching characters of last best match

        for (int n = 0; m_rgOptions[n].nId >= 0; ++n) {
            // the option table must use hyphens as the option character, 
            // the slash character is converted to a hyphen for testing.
            SO_ASSERT(m_rgOptions[n].pszArg[0] != (SOCHAR) '/');

            int nMatchLen = CalcMatch(m_rgOptions[n].pszArg, a_pszOption);
            if (nMatchLen == -1) {
                return n;
            }
            if (nMatchLen > 0 && nMatchLen >= nBestMatchLen) {
                nLastMatchLen = nBestMatchLen;
                nBestMatchLen = nMatchLen;
                nBestMatch = n;
            }
        }

        // only partial matches or no match gets to here, ensure that we
        // don't return a partial match unless it is a clear winner
        if ((m_nFlags & SO_O_EXACT) || nBestMatch == -1) {
            return SO_OPT_INVALID;
        }
        return (nBestMatchLen > nLastMatchLen) ? nBestMatch : SO_OPT_MULTIPLE;
    }

    // Find the '=' character within a string.
    SOCHAR * FindEquals(SOCHAR *s) const {
        while (*s && *s != (SOCHAR)'=') ++s;
        return *s ? s : 0;
    }

    // calculate the number of characters that match (case-sensitive)
    // 0 = no match, > 0 == number of characters, -1 == perfect match
    int CalcMatch(const SOCHAR *pszSource, const SOCHAR *pszTest) const {
        if (!pszSource || !pszTest) {
            return 0;
        }

        // match and skip leading hyphens
        while (*pszSource == (SOCHAR)'-' && *pszSource == *pszTest) {
            ++pszSource; ++pszTest;
        }
        if (*pszSource == (SOCHAR)'-' || *pszTest == (SOCHAR)'-') {
            return 0;
        }

        // find matching number of characters in the strings
        int nLen = 0;
        while (*pszSource && *pszSource == *pszTest) {
            ++pszSource; ++pszTest; ++nLen;
        }

        // if we have exhausted the source...
        if (!*pszSource) {
            // and the test strings, then it's a perfect match
            if (!*pszTest) {
                return -1;
            }

            // otherwise the match failed as the test is longer than
            // the source. i.e. "--mant" will not match the option "--man".
            return 0;
        }

        // if we haven't exhausted the test string then it is not a match
        // i.e. "--mantle" will not best-fit match to "--mandate" at all.
        if (*pszTest) {
            return 0;
        }

        // partial match to the current length of the test string
        return nLen;
    }

private:
    const SOption * m_rgOptions;     // pointer to options table as passed in to soInit()
    int             m_nFlags;       // flags for parsing the command line
    int             m_nOptionIdx;    // index of the current option in argv
    int             m_nOptionId;     // id of the current option (or -1 if invalid option)
    int             m_nNextOption;   // index of the next option to be processed
    int             m_nLastArg;      // last unprocessed argument, after this are files
    int             m_argc;          // argc to process
    SOCHAR **       m_argv;          // argv (rearranged during processing)
    SOCHAR *        m_pszOptionText; // text of the current option, e.g. "-f" or "--file"
    SOCHAR *        m_pszOptionArg;  // argument for the current option, e.g. "c:\blah.txt" (or 0 if no argument)
    SOCHAR *        m_pszClump;      // processing of clumped single character options
    SOCHAR          m_szShort[3];    // extract short option text from clumps and combined arguments
    ESOError        m_nLastError;    // error status from the last call
};

// we supply both ASCII and WIDE char versions, plus a
// SOCHAR style that changes depending on the build setting
typedef CSimpleOptTempl<char>    CSimpleOptA;
typedef CSimpleOptTempl<wchar_t> CSimpleOptW;
#if defined(_UNICODE)
# define CSimpleOpt CSimpleOptW
#else
# define CSimpleOpt CSimpleOptA
#endif

#endif // INCLUDED_SimpleOpt
