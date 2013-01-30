function parseHTML(innerHTML)
    {

        var good_browser = (window.opera || navigator.product == 'Gecko');
        var regex = /^([\s\S]*?)<script([\s\S]*?)>([\s\S]*?)<\/script>([\s\S]*)$/i;
        var regex_src = /src=["'](.*?)["']/i;
        var matches, id, script, output = '', subject = innerHTML;
        var scripts = [];
        
        while (true) {
            matches = regex.exec(subject);
            if (matches && matches[0]) {
                subject = matches[4];
                id = 'ih_' + Math.round(Math.random()*9999) + '_' + Math.round(Math.random()*9999);

                var startLen = matches[3].length;
                script = matches[3].replace(/document\.write\(([\s\S]*?)\)/ig, 
                    'document.getElementById("' + id + '").innerHTML+=$1');
                output += matches[1];
                if (startLen != script.length) {
                        output += '<span id="' + id + '"></span>';
                }
                
		alert(script);
                output += '<script' + matches[2] + '>' + script + '</script>';
                if (false  || good_browser) {
                    continue;
                }
                if (script) {
                    scripts.push(script);
                }
                if (regex_src.test(matches[2])) {
                    var script_el = document.createElement("SCRIPT");
                    var atts_regex = /(\w+)=["'](.*?)["']([\s\S]*)$/;
                    var atts = matches[2];
                    for (var i = 0; i < 5; i++) { 
                        var atts_matches = atts_regex.exec(atts);
                        if (atts_matches && atts_matches[0]) {
                            script_el.setAttribute(atts_matches[1], atts_matches[2]);
                            atts = atts_matches[3];
                        } else {
                            break;
                        }
                    }
                    scripts.push(script_el);
                }
            } else {
                output += subject;
                break;
            }
        }
        innerHTML = output;
	alert(output);

        if (!good_browser) {
            for(var i = 0; i < scripts.length; i++) {
                if (true) {
                    scripts[i] = scripts[i].replace(/^\s*<!(\[CDATA\[|--)|((\/\/)?--|\]\])>\s*$/g, '');
                    window.eval(scripts[i]);
                }
            }
        }
	alert('step1' + scripts.length);
        return innerHTML;
    }
