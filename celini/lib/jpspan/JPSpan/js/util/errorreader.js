// $Id
// Used to display localized error messages
// Note the errorList is populated by Include
function JPSpan_Util_ErrorReader() {}

// Get a message given it's code
// @param int error code (use e.code exception property)
// @return mixed string error message if found or false if not found
JPSpan_Util_ErrorReader.prototype.getMessage = function(code) {
    if ( this.errorList[code] ) {
        return this.errorList[code];
    } else {
        return false;
    }
}