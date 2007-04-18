// $Id: typeof.js,v 1.1 2004/11/22 10:51:57 harryf Exp $
// Taken from http://www.webreference.com/dhtml/column68/ by Peter Belesis
function JPSpan_Util_typeof( vExpression ) {

    var sTypeOf = typeof vExpression;
    
    if( sTypeOf == "function" ) {
        var sFunction = vExpression.toString();
        if( ( /^\/.*\/$/ ).test( sFunction ) ) {
            return "regexp";
        } else if( ( /^\[object.*\]$/i ).test( sFunction ) ) {
            sTypeOf = "object"
        }
    }
    
    if( sTypeOf != "object" ) {
        return sTypeOf;
    }
    
    switch( vExpression ) {
        case null:
            return "null";
        case window:
            return "window";
        case window.event:    
            return "event";
    }
    
    if( window.event && ( event.type == vExpression.type ) ) {
        return "event";
    }
    
    var fConstructor = vExpression.constructor;
    
    if( fConstructor != null ) {
        switch( fConstructor ) {
            case Array:
                sTypeOf = "array";
                break;
            case Date:
                return "date";
            case RegExp:
                return "regexp";
            case Object:
                sTypeOf = "jsobject";
                break;
            case ReferenceError:
                return "error";
            default:
                var sConstructor = fConstructor.toString();
                var aMatch = sConstructor.match( /\s*function (.*)\(/ );
                if( aMatch != null ) {
                    return aMatch[ 1 ];
                }
            
        }
    }

    var nNodeType = vExpression.nodeType;
    if( nNodeType != null ) {
        switch( nNodeType ) {
            case 1:
                if( vExpression.item == null ) {
                    return "domelement";
                }
                break;
            case 3:
                return "textnode";
        }
    }
    
    if( vExpression.toString != null ) {
    
        var sExpression = vExpression.toString();
        
        var aMatch = sExpression.match( /^\[object (.*)\]$/i );
        
        if( aMatch != null ) {
        
            var sMatch = aMatch[ 1 ];
            switch( sMatch.toLowerCase() ) {
                case "event":
                    return "event";
                case "math":
                    return "math";
                case "error":    
                    return "error";
                case "mimetypearray":
                    return "mimetypecollection";
                case "pluginarray":
                    return "plugincollection";
                case "windowcollection":
                    return "window";
                case "nodelist":
                case "htmlcollection":
                case "elementarray":
                    return "domcollection";
            }
        }
    }
    
    if( vExpression.moveToBookmark && vExpression.moveToElementText ) {
        return "textrange";
    } else if( vExpression.callee != null ) {
        return "arguments";
    } else if( vExpression.item != null ) {
        return "domcollection";
    }
    
    return sTypeOf;
}
