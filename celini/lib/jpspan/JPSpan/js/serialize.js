// $Id: serialize.js,v 1.5 2004/11/21 11:14:05 harryf Exp $
// Notes:
// - Watch out for recursive references - call inside a try/catch block if uncertain
// - Objects are serialized to PHP class name JPSpan_Object by default
// - Errors are serialized to PHP class name JPSpan_Error by default
//
// See discussion below for notes on Javascript reflection
// http://www.webreference.com/dhtml/column68/
function JPSpan_Serialize(Encoder) {
    this.Encoder = Encoder;
    this.typeMap = new Object();
};

JPSpan_Serialize.prototype = {

    typeMap: null,
    
    addType: function(cname, callback) {
        this.typeMap[cname] = callback;
    },
    
    serialize: function(v) {
    
        switch(typeof v) {
            //-------------------------------------------------------------------
            case 'object':
            
                // It's a null value
                if ( v === null ) {
                    return this.Encoder.encodeNull();
                }
                
                // Get the constructor
                var c = v.constructor;
                
                if (c != null ) {
                
                    // It's an array
                    if ( c == Array ) {
                        return this.Encoder.encodeArray(v,this);
                    } else {
                    
                        // Get the class name
                        var match = c.toString().match( /\s*function (.*)\(/ );

                        if ( match == null ) {
                            return this.Encoder.encodeObject(v,this,'JPSpan_Object');
                        }
                        
                        // Strip space for IE
                        var cname = match[1].replace(/\s/,'');
                        
                        // Has the user registers a callback for serializing this class?
                        if ( this.typeMap[cname] ) {
                            return this.typeMap[cname](v, this, cname);
                            
                        } else {
                            // Check for error objects
                            var match = cname.match(/Error/);
                        
                            if ( match == null ) {
                                return this.Encoder.encodeObject(v,this,'JPSpan_Object');
                            } else {
                                return this.Encoder.encodeError(v,this,'JPSpan_Error');
                            }

                        }
                    }
                } else {
                    // Return null if constructor is null
                    return this.Encoder.encodeNull();
                }
            break;
            
            //-------------------------------------------------------------------
            case 'string':
                return this.Encoder.encodeString(v);
            break;
            
            //-------------------------------------------------------------------
            case 'number':
                if (Math.round(v) == v) {
                    return this.Encoder.encodeInteger(v);
                } else {
                    return this.Encoder.encodeDouble(v);
                };
            break;
            
            //-------------------------------------------------------------------
            case 'boolean':
                if (v == true) {
                    return this.Encoder.encodeTrue();
                } else {
                    return this.Encoder.encodeFalse();
                };
            break;
            
            //-------------------------------------------------------------------
            default:
                return this.Encoder.encodeNull();
            break;
        }
    }
}
