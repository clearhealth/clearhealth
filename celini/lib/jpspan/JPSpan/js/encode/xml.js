/**@
* include 'serialize.js';
*/
// $Id: xml.js,v 1.7 2004/11/19 21:56:47 harryf Exp $
// See: http://jpspan.sourceforge.net/wiki/doku.php?id=encoding
function JPSpan_Encode_Xml() {
    this.Serialize = new JPSpan_Serialize(this);
};

JPSpan_Encode_Xml.prototype = {

    // Used by rawpost request objects
    contentType: 'text/xml; charset=UTF-8',

    encode: function(data) {
        return '<?xml version="1.0" encoding="UTF-8"?><r>'+this.Serialize.serialize(data)+'</r>';
    },
    
    encodeInteger: function(v) {
        return '<i v="'+v+'"/>';
    },
    
    encodeDouble: function(v) {
        return '<d v="'+v+'"/>';
    },
    
    // Need UFT-8 encoding?
    encodeString: function(v) {
        return '<s>'+v.replace(/&/g, '&amp;').replace(/</g, '&lt;')+'</s>';
    },
    
    encodeNull: function() {
        return '<n/>';
    },
    
    encodeTrue: function() {
        return '<b v="1"/>';
    },
    
    encodeFalse: function() {
        return '<b v="0"/>';
    },
    
    // Arrays being with indexed values - properties added second
    encodeArray: function(v, Serializer) {
        var indexed = new Array();
        var a = '';
        for (var i=0; i<v.length; i++) {
            indexed[i] = true;
            a += '<e k="'+i+'">'+Serializer.serialize(v[i])+'</e>';
        };

        for ( var prop in v ) {
            if ( indexed[prop] ) {
                continue;
            };
            // Assumes prop obeys Javascript naming rules
            a += '<e k="'+prop+'">'+Serializer.serialize(v[prop])+'</e>';
        };
        return '<a>'+a+'</a>';
    },
    
    encodeObject: function(v, Serializer, cname) {
        var o='';
        for (var prop in v) {
            o += '<e k="'+prop+'">'+Serializer.serialize(v[prop])+'</e>';
        };
        return '<o c="'+cname.toLowerCase()+'">'+o+'</o>';
    },
    
    encodeError: function(v, Serializer, cname) {
        var e = new Object();
        if ( !v.name ) {
            e.name = cname;
            e.message = v.description;
        } else {
            e.name = v.name;
            e.message = v.message;
        };
        return this.encodeObject(e,Serializer,cname);
    }
}
