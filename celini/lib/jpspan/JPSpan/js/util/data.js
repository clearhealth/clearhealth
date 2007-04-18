/**@
* include 'serialize.js';
*/
// $Id: data.js,v 1.2 2004/11/12 22:27:23 harryf Exp $
function JPSpan_Util_Data() {
    this.Serialize = new JPSpan_Serialize(this);
    this.indent = '';
};

JPSpan_Util_Data.prototype = {
    dump: function(data) {
        return this.Serialize.serialize(data);
    },
    
    encodeInteger: function(v) {
        return 'Integer: '+v+"\n";
    },
    
    encodeDouble: function(v) {
        return 'Double: '+v+"\n";
    },
    
    encodeString: function(v) {
        return "String("+v.length+"): "+v+"\n";
    },
    
    encodeNull: function() {
        return "Null\n";
    },
    
    encodeTrue: function() {
        return "Boolean(true)\n"
    },
    
    encodeFalse: function() {
        return "Boolean(false)\n"
    },
    
    encodeArray: function(v, Serializer) {
        var a=v;
        var indexed = new Array();
        var out="Array("+a.length+")\n";
        this.indent += "  ";
        if ( a.length>0 ) {
            for (var i=0; i < a.length; i++) {
                indexed[i] = true;
                out+=this.indent+"["+i+"]";
                if ( (a[i]+'') == 'undefined') {
                    out+= " = undefined\n";
                    continue;
                };
                out+= " = "+Serializer.serialize(a[i])+"\n";
            };
        };
        var assoc='';
        for ( var prop in a ) {
            if ( indexed[prop] ) {
                continue;
            };
            assoc+=this.indent+"[\""+prop+"\"]";
            if ( (a[prop]+'') == 'undefined') {
                assoc+= " = undefined\n";
                continue;
            };
            assoc+= " = "+Serializer.serialize(a[prop])+"\n";
        };
        if ( assoc.length > 0 ) {
            out += assoc;
        };
        this.indent = this.indent.substr(0,this.indent.length-2);
        return out;
    },
    
    encodeObject: function(v, Serializer, cname) {
        var o=v;
        if (o==null) return "Null\n";
        var out="Object("+cname+")\n";
        this.indent += "  ";
        for (var prop in o) {
            out+=this.indent+"."+prop+" = ";
            if (o[prop]==null) {
                out+="null\n";
                continue;
            };
            out+=Serializer.serialize(o[prop])+"\n";
        };
        this.indent = this.indent.substr(0,this.indent.length-2);
        return out;
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
};

