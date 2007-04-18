/**@
* include 'serialize.js';
*/
// $Id: php.js,v 1.3 2004/11/12 15:41:10 harryf Exp $
// Notes:
// - strings will only have ASCII characters encoded. Anything else
//   will be thrown out
// See: http://jpspan.sourceforge.net/wiki/doku.php?id=encoding
function JPSpan_Encode_PHP() {
    this.Serialize = new JPSpan_Serialize(this);
};

JPSpan_Encode_PHP.prototype = {

    // Used by rawpost request objects
    contentType: 'text/plain; charset=US-ASCII',
    
    encode: function(data) {
        return this.Serialize.serialize(data);
    },
    
    encodeInteger: function(v) {
        return 'i:'+v+';';
    },
    
    encodeDouble: function(v) {
        return 'd:'+v+';';
    },
    
    encodeString: function(v) {
        var s = ''
        for(var n=0; n<v.length; n++) {
            var c=v.charCodeAt(n);
            // Ignore everything but ASCII
            if (c<128) {
                s += String.fromCharCode(c);
            }
        }
        return 's:'+s.length+':"'+s+'";';
    },
    
    encodeNull: function() {
        return 'N;';
    },
    
    encodeTrue: function() {
        return 'b:1;';
    },
    
    encodeFalse: function() {
        return 'b:0;';
    },
    
    encodeArray: function(v, Serializer) {
        var indexed = new Array();
        var count = v.length;
        var s = '';
        for (var i=0; i<v.length; i++) {
            indexed[i] = true;
            s += 'i:'+i+';'+Serializer.serialize(v[i]);
        };

        for ( var prop in v ) {
            if ( indexed[prop] ) {
                continue;
            };
            s += Serializer.serialize(prop)+Serializer.serialize(v[prop]);
            count++;
        };
        
        s = 'a:'+count+':{'+s;
        s += '}';
        return s;
    },
    
    encodeObject: function(v, Serializer, cname) {
        var s='';
        var count=0;
        for (var prop in v) {
            s += 's:'+prop.length+':"'+prop+'";';
            if (v[prop]!=null) {
                s += Serializer.serialize(v[prop]);
            } else {
                s +='N;';
            };
            count++;
        };
        s = 'O:'+cname.length+':"'+cname.toLowerCase()+'":'+count+':{'+s+'}';   
        return s;
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