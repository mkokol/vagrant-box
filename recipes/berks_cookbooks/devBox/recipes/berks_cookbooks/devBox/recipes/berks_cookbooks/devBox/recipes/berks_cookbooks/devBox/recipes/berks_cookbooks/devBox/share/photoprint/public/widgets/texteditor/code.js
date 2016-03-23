/**********************************************************************
FreeRichTextEditor.com Version 1.00.
**********************************************************************/
var oldContentRte = '';
// Width of the rich text editor.
rteWidth = "100%";
// Name of the IFRAME (content editor).
rteName = "freeRTE";
// Name of the hidden form field.
rteFormName = "freeRTE_content";
// Height of the rich text editor.
rteHeight = "272";
// Height of the rich text editor inputed text.
rteMaxHeight = "485";
// Path to the images folder.
rteImagePath = basePath + "/public/theme/rte/";
// Font Face Menu (Arial, Verdana etc etc).
rteFontFace = true;
// Font Size Menu (1, 2, etc etc).
rteFontSize = true;
// Font Color Menu.
rteFontColor = true;
// Bold Text Button.
rteBold = true;
// Italicize Text Button.
rteItalic = true;
// Underline Text Button.
rteUnderline = true;
// Left Justify Button.
rteLeftAlign = true;
// Center Justify Button.
rteCenterAlign = true;
// Right Justify Button.
rteRightAlign = true;
// Full Justify Button.
rteFullAlign = true;
// Insert Horizontal Rule Button.
rteHorizontalRule = true;

function returnRezulText(){
    if (document.getElementById(rteFormName).style.display == ""){
        var newHTML = getXHTML(trim(document.getElementById(rteFormName).value));
    }else{
        var newHTML = getXHTML(trim(document.getElementById(rteName).contentWindow.document.body.innerHTML));
    }
    pattern = /<div[^>]*border: 1px dotted red[^>]*>.*<\/form><\/div>/gi;
    matchesArray = newHTML.match(pattern);
    if (matchesArray != null){
        for (i=0; i<matchesArray.length; i++){
            pattern2 = /<div[^>]*border: 1px dotted red[^>]*>/gi;
            pattern3 = /<\/div>/gi;
            replacement = matchesArray[i];
            replacement = replacement.replace(pattern2, "");
            replacement = replacement.replace(pattern3, "");
            if (document.getElementById(rteFormName).style.display == ""){
                newHTML = document.getElementById(rteFormName).value.replace(matchesArray[i], replacement);
            }else{
                newHTML = document.getElementById(rteName).contentWindow.document.body.innerHTML.replace(matchesArray[i], replacement);
            }
        }
    }
    pattern = /<table[^>]*class=\"rte_tbl\"[^>]*>/gi;
    matchesArray = newHTML.match(pattern);

    if (matchesArray != null){
        for (i=0; i<matchesArray.length; i++){
            pattern2 = /class=\"rte_tbl\"/gi;
            replacement = matchesArray[i];
            replacement = replacement.replace(pattern2, "");
            newHTML = newHTML.replace(matchesArray[i], replacement);
        }
    }

    document.getElementById("postcardText").innerHTML = newHTML;
    $("#mbox-overlay").hide();
    $("#mbox-overlay").remove();
    $("#text-editor").css({
        display: 'none'
    });
}
function closeWithOutRezulText(){
    $("#mbox-overlay").hide();
    $("#mbox-overlay").remove();
    $("#text-editor").css({
        display: 'none'
    });
}
function getXHTML(data){
    return new Html2Xhtml(data).parse()
};
function Html2Xhtml(data){
    this.data=data||''
};
Html2Xhtml.prototype.setHTML=function(data){
    this.data=data||this.data
};
Html2Xhtml.prototype.parse=function(){
    var state=0;
    var xhtml='';
    var p=0;
    var unget=false;
    var tagname='';
    var attrname='';
    var attrval='';
    var quot='';
    var data=this.data;
    var len=data.length;
    var phpval='';
    var tagtype=0;
    var insidepre=false;
    while(1){
        if(p>=len&&!unget){
            return xhtml
            }
            if(unget){
            unget=false
            }else{
            var c=data.substr(p++,1)
            }
            switch(state){
            case 0:
                if(c=='<'){
                state=1;
                break
            }
            var cc=c.charCodeAt();
                if(Html2Xhtml.charEntities[cc]){
                xhtml+='&'+Html2Xhtml.charEntities[cc]+';'
                }else{
                xhtml+=c
                }
                break;
            case 1:
                if(/[a-zA-Z]/.test(c)){
                state=2;
                tagtype=1;
                tagname=c.toLowerCase();
                break
            }
            if(c=='/'){
                state=2;
                tagtype=-1;
                break
            }
            if(c=='!'){
                if(data.substr(p,2)=='--'){
                    xhtml+='<!--';
                    p+=2;
                    state=9;
                    break
                }
                xhtml+='<!';
                state=10;
                break
            }
            if(c=='?'){
                state=11;
                xhtml+='<'+'?';
                break
            }
            xhtml+='&lt;';
            unget=true;
            state=0;
            break;
            case 2:
                if(Html2Xhtml.isSpaceChar[c]){
                xhtml+=(!insidepre&&tagtype>0&&Html2Xhtml.hasNLBefore[tagname]&&xhtml.length&&xhtml.substr(xhtml.length-1,1)!='\n'?'\n':'')+(tagtype>0?'<':'</')+tagname;
                state=3;
                break
            }
            if(c=='/'){
                xhtml+=(!insidepre&&tagtype>0&&Html2Xhtml.hasNLBefore[tagname]&&xhtml.length&&xhtml.substr(xhtml.length-1,1)!='\n'?'\n':'')+(tagtype>0?'<':'</')+tagname;
                if(data.substr(p,1)!='>'){
                    state=3;
                    break
                }
                state=4;
                break
            }
            if(c=='>'){
                xhtml+=(!insidepre&&tagtype>0&&Html2Xhtml.hasNLBefore[tagname]&&xhtml.length&&xhtml.substr(xhtml.length-1,1)!='\n'?'\n':'')+(tagtype>0?'<':'</')+tagname;
                unget=true;
                state=4;
                break
            }
            tagname+=c.toLowerCase();
                break;
            case 3:
                if(Html2Xhtml.isSpaceChar[c]){
                break
            }
            if(c=='/'){
                if(data.substr(p,1)!='>'){
                    break
                }
                state=4;
                break
            }
            if(c=='>'){
                unget=true;
                state=4;
                break
            }
            attrname=c.toLowerCase();
                attrval='';
                state=5;
                break;
            case 4:
                xhtml+=(Html2Xhtml.isEmptyTag[tagname]?' />':'>')+(!insidepre&&tagtype<0&&Html2Xhtml.hasNLAfter[tagname]&&p<len&&data.substr(p,1)!='\n'?'\n':'');
                if(tagtype>0&&Html2Xhtml.dontAnalyzeContent[tagname]){
                state=13;
                attrname=attrval=quot='';
                tagtype=0;
                break
            }
            if(tagname=='pre'){
                insidepre=!insidepre
                }
                state=0;
            tagname=attrname=attrval=quot='';
            tagtype=0;
            break;
            case 5:
                if(Html2Xhtml.isSpaceChar[c]){
                xhtml+=' '+attrname;
                if(Html2Xhtml.isEmptyAttr[attrname]){
                    xhtml+='="'+attrname+'"'
                    }
                    state=3;
                break
            }
            if(c=='/'){
                xhtml+=' '+attrname;
                if(Html2Xhtml.isEmptyAttr[attrname]){
                    xhtml+='="'+attrname+'"'
                    }
                    if(data.substr(p,1)!='>'){
                    state=3;
                    break
                }
                state=4;
                break
            }
            if(c=='>'){
                xhtml+=' '+attrname;
                if(Html2Xhtml.isEmptyAttr[attrname]){
                    xhtml+='="'+attrname+'"'
                    }
                    unget=true;
                state=4;
                break
            }
            if(c=='='){
                xhtml+=' '+attrname+'=';
                state=6;
                break
            }
            if(c=='"'||c=="'"){
                attrname+='?'
                }else{
                attrname+=c.toLowerCase()
                }
                break;
            case 6:
                if(Html2Xhtml.isSpaceChar[c]){
                xhtml+=(Html2Xhtml.isEmptyAttr[attrname]?'"'+attrname+'"':'""');
                state=3;
                break
            }
            if(c=='>'){
                xhtml+=(Html2Xhtml.isEmptyAttr[attrname]?'"'+attrname+'"':'""');
                unget=true;
                state=4;
                break
            }
            if(c=='/'&&data.substr(p,1)=='>'){
                xhtml+=(Html2Xhtml.isEmptyAttr[attrname]?'"'+attrname+'"':'""');
                state=4;
                break
            }
            if(c=='"'||c=="'"){
                quot=c;
                state=8;
                break
            }
            attrval=c;
            state=7;
            break;
            case 7:
                if(Html2Xhtml.isSpaceChar[c]){
                xhtml+='"'+Html2Xhtml.escapeQuot(attrval,'"')+'"';
                state=3;
                break
            }
            if(c=='/'&&data.substr(p,1)=='>'){
                xhtml+='"'+Html2Xhtml.escapeQuot(attrval,'"')+'"';
                state=4;
                break
            }
            if(c=='>'){
                unget=true;
                xhtml+='"'+Html2Xhtml.escapeQuot(attrval,'"')+'"';
                state=4;
                break
            }
            attrval+=c;
            break;
            case 8:
                if(c==quot){
                xhtml+='"'+Html2Xhtml.escapeQuot(attrval,'"')+'"';
                state=3;
                break
            }
            attrval+=c;
            break;
            case 9:
                if(c=='-'&&data.substr(p,2)=='->'){
                p+=2;
                xhtml+='-->';
                state=0;
                break
            }
            xhtml+=c;
            break;
            case 10:
                if(c=='>'){
                state=0
                }
                xhtml+=c;
            break;
            case 11:
                if(c=="'"||c=='"'){
                quot=c;
                state=12;
                break
            }
            if(c=='?'&&data.substr(p,1)=='>'){
                state=0;
                xhtml+='?'+'>';
                p++;
                break
            }
            xhtml+=c;
            break;
            case 12:
                if(c==quot){
                state=11;
                xhtml+=quot+Html2Xhtml.escapeQuot(phpval,quot)+quot;
                phpval=quot='';
                break
            }
            phpval+=c;
            break;
            case 13:
                if(c=='<'&&data.substr(p,tagname.length+1).toLowerCase()=='/'+tagname){
                unget=true;
                state=0;
                tagname='';
                break
            }
            if(tagname=='textarea'){
                xhtml+=Html2Xhtml.escapeHTMLChar(c)
                }else{
                xhtml+=c
                }
                break
            }
            }
    return xhtml
};
Html2Xhtml.escapeQuot=function(str,quot){
    if(!quot){
        quot='"'
    }
    if(quot=='"'){
        return str.replace(/"/ig,'\\"')
    }
    return str.replace(/'/ig,"\\'")
};
Html2Xhtml.escapeHTMLChar=function(c){
    if(c=='&'){
        return'&amp;'
    }
    if(c=='<'){
        return'&lt;'
    }
    if(c=='>'){
        return'&gt;'
    }
    var cc=c.charCodeAt();
    if(Html2Xhtml.charEntities[cc]){
        return'&'+Html2Xhtml.charEntities[cc]+';'
    }else{
        return c
    }
};
Html2Xhtml.isSpaceChar={
    ' ':1, '\r':1, '\n':1, '\t':1
};
Html2Xhtml.isEmptyTag={
    'area':1, 'base':1, 'basefont':1, 'br':1, 'hr':1, 'img':1, 'input':1, 'link':1, 'meta':1, 'param':1
};
Html2Xhtml.isEmptyAttr={
    'checked':1, 'compact':1, 'declare':1, 'defer':1, 'disabled':1, 'ismap':1, 'multiple':1, 'noresize':1, 'nosave':1,
    'noshade':1, 'nowrap':1, 'readonly':1, 'selected':1
};

Html2Xhtml.hasNLBefore={
    'div':1, 'p':1, 'table':1, 'tbody':1, 'tr':1, 'td':1, 'th':1, 'title':1, 'head':1,'body':1, 'script':1, 'comment':1,
    'li':1, 'meta':1, 'h1':1, 'h2':1, 'h3':1, 'h4':1, 'h5':1, 'h6':1, 'hr':1, 'ul':1, 'ol':1, 'option':1, 'link':1
};
Html2Xhtml.hasNLAfter={
    'html':1, 'head':1, 'body':1, 'p':1, 'th':1, 'style':1
};
Html2Xhtml.dontAnalyzeContent={
    'textarea':1, 'script':1, 'style':1
};
Html2Xhtml.charEntities={
    160:'nbsp', 161:'iexcl', 162:'cent', 163:'pound', 164:'curren', 165:'yen', 166:'brvbar', 167:'sect',
    168:'uml', 169:'copy', 170:'ordf', 171:'laquo', 172:'not', 173:'shy', 174:'reg', 175:'macr', 176:'deg',
    177:'plusmn', 178:'sup2', 179:'sup3', 180:'acute', 181:'micro', 182:'para', 183:'middot', 184:'cedil',
    185:'sup1', 186:'ordm', 187:'raquo', 188:'frac14', 189:'frac12', 190:'frac34', 191:'iquest', 192:'agrave',
    193:'aacute', 194:'acirc', 195:'atilde', 196:'auml', 197:'aring', 198:'aelig', 199:'ccedil', 200:'egrave',
    201:'eacute', 202:'ecirc', 203:'euml', 204:'igrave', 205:'iacute', 206:'icirc', 207:'iuml', 208:'eth',
    209:'ntilde', 210:'ograve', 211:'oacute', 212:'ocirc', 213:'otilde', 214:'ouml', 215:'times', 216:'oslash',
    217:'ugrave', 218:'uacute', 219:'ucirc', 220:'uuml', 221:'yacute', 222:'thorn', 223:'szlig', 224:'agrave',
    225:'aacute', 226:'acirc', 227:'atilde', 228:'auml', 229:'aring', 230:'aelig', 231:'ccedil', 232:'egrave',
    233:'eacute', 234:'ecirc', 235:'euml', 236:'igrave', 237:'iacute', 238:'icirc', 239:'iuml', 240:'eth',
    241:'ntilde', 242:'ograve', 243:'oacute', 244:'ocirc', 245:'otilde', 246:'ouml', 247:'divide', 248:'oslash',
    249:'ugrave', 250:'uacute', 251:'ucirc', 252:'uuml', 253:'yacute', 254:'thorn', 255:'yuml', 338:'oelig',
    339:'oelig', 352:'scaron', 353:'scaron', 376:'yuml', 710:'circ', 732:'tilde', 8194:'ensp', 8195:'emsp',
    8201:'thinsp', 8204:'zwnj', 8205:'zwj', 8206:'lrm', 8207:'rlm', 8211:'ndash', 8212:'mdash', 8216:'lsquo',
    8217:'rsquo', 8218:'sbquo', 8220:'ldquo', 8221:'rdquo', 8222:'bdquo', 8224:'dagger', 8225:'dagger', 8240:'permil',
    8249:'lsaquo', 8250:'rsaquo', 8364:'euro', 402:'fnof', 913:'alpha', 914:'beta', 915:'gamma', 916:'delta',
    917:'epsilon', 918:'zeta', 919:'eta', 920:'theta', 921:'iota', 922:'kappa', 923:'lambda', 924:'mu', 925:'nu',
    926:'xi', 927:'omicron', 928:'pi', 929:'rho', 931:'sigma', 932:'tau', 933:'upsilon', 934:'phi', 935:'chi',
    936:'psi', 937:'omega', 945:'alpha', 946:'beta', 947:'gamma', 948:'delta', 949:'epsilon', 950:'zeta', 951:'eta',
    952:'theta', 953:'iota', 954:'kappa', 955:'lambda', 956:'mu', 957:'nu', 958:'xi', 959:'omicron', 960:'pi',
    961:'rho', 962:'sigmaf', 963:'sigma', 964:'tau', 965:'upsilon', 966:'phi', 967:'chi', 968:'psi', 969:'omega',
    977:'thetasym', 978:'upsih', 982:'piv', 8226:'bull', 8230:'hellip', 8242:'prime', 8243:'prime', 8254:'oline',
    8260:'frasl', 8472:'weierp', 8465:'image', 8476:'real', 8482:'trade', 8501:'alefsym', 8592:'larr', 8593:'uarr',
    8594:'rarr', 8595:'darr', 8596:'harr', 8629:'crarr', 8656:'larr', 8657:'uarr', 8658:'rarr', 8659:'darr',
    8660:'harr', 8704:'forall', 8706:'part', 8707:'exist', 8709:'empty', 8711:'nabla', 8712:'isin', 8713:'notin',
    8715:'ni', 8719:'prod', 8721:'sum', 8722:'minus', 8727:'lowast', 8730:'radic', 8733:'prop', 8734:'infin',
    8736:'ang', 8743:'and', 8744:'or', 8745:'cap', 8746:'cup', 8747:'int', 8756:'there4', 8764:'sim', 8773:'cong',
    8776:'asymp', 8800:'ne', 8801:'equiv', 8804:'le', 8805:'ge', 8834:'sub', 8835:'sup', 8836:'nsub', 8838:'sube',
    8839:'supe', 8853:'oplus', 8855:'otimes', 8869:'perp', 8901:'sdot', 8968:'lceil', 8969:'rceil', 8970:'lfloor',
    8971:'rfloor', 9001:'lang', 9002:'rang', 9426:'copy', 9674:'loz', 9824:'spades', 9827:'clubs', 9829:'hearts',
    9830:'diams'
};
function trim(str) {
    if (typeof str != "string") return str;
    str = str.replace(/^\s+|\s+$/g, "");
    return str;
}
function rteGetOffsetTop(elm) {
    var mOffsetTop = elm.offsetTop;
    var mOffsetParent = elm.offsetParent;
    while(mOffsetParent) {
        mOffsetTop += mOffsetParent.offsetTop;
        mOffsetParent = mOffsetParent.offsetParent;
    }
    return mOffsetTop;
};
function rteGetOffsetLeft(elm) {
    var mOffsetLeft = elm.offsetLeft;
    var mOffsetParent = elm.offsetParent;
    while(mOffsetParent) {
        mOffsetLeft += mOffsetParent.offsetLeft;
        mOffsetParent = mOffsetParent.offsetParent;
    }
    return mOffsetLeft;
};
function rteHideMenus() {
    rteMouseOutFontFaceMenu();
    document.getElementById("fontface3").style.display = "none";
    rteMouseOutFontSizeMenu();
    document.getElementById("fontsize3").style.display = "none";
    rteMouseOutFontColorMenu();
    document.getElementById("fontcolor3").style.display = "none";
    checkInputTextSize();
};
function rteColorClick(hexcolor) {
    rteHideMenus();
    document.getElementById(rteName).contentWindow.document.execCommand("forecolor", false, hexcolor);
    document.getElementById("fontcolor4").style.backgroundColor = hexcolor;
};
function rteMouseOverMenuFontColorContents() {
    this.className = "rtedropdown14";
};
function rteMouseOutMenuFontColorContents() {
    this.className = "rtedropdown13";
};
function rteMouseOverMenuContents() {
    this.style.color = "#FFFFFF";
    this.style.backgroundColor = "#316AC5";
    document.getElementById(rteName).contentWindow.focus();
};
function rteMouseOutMenuContents() {
    this.style.color = "#000000";
    this.style.backgroundColor = "#FFFFFF";
};
function rteMouseDownMenuContents() {
    if (this.innerHTML == "Header 1") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h1>");
        document.getElementById("format1").innerHTML = "Header 1";
    } else if (this.innerHTML == "Header 2") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h2>");
        document.getElementById("format1").innerHTML = "Header 2";
    } else if (this.innerHTML == "Header 3") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h3>");
        document.getElementById("format1").innerHTML = "Header 3";
    } else if (this.innerHTML == "Header 4") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h4>");
        document.getElementById("format1").innerHTML = "Header 4";
    } else if (this.innerHTML == "Header 5") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h5>");
        document.getElementById("format1").innerHTML = "Header 5";
    } else if (this.innerHTML == "Header 6") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<h6>");
        document.getElementById("format1").innerHTML = "Header 6";
    } else if (this.innerHTML == "Paragraph") {
        document.getElementById(rteName).contentWindow.document.execCommand("formatblock", false, "<p>");
        document.getElementById("format1").innerHTML = "Paragraph";
    } else if (this.innerHTML == "Arial") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "arial");
        document.getElementById("fontface1").innerHTML = "Arial";
    } else if (this.innerHTML == "Arial Black") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "arial black");
        document.getElementById("fontface1").innerHTML = "Arial Black";
    } else if (this.innerHTML == "Arial Narrow") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "arial narrow");
        document.getElementById("fontface1").innerHTML = "Arial Narrow";
    } else if (this.innerHTML == "Courier New") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "courier new");
        document.getElementById("fontface1").innerHTML = "Courier New";
    } else if (this.innerHTML == "Century Gothic") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "century gothic");
        document.getElementById("fontface1").innerHTML = "Century Gothic";
    } else if (this.innerHTML == "Comic Sans MS") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "comic sans ms");
        document.getElementById("fontface1").innerHTML = "Comic Sans MS";
    } else if (this.innerHTML == "Impact") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "impact");
        document.getElementById("fontface1").innerHTML = "Impact";
    } else if (this.innerHTML == "Tahoma") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "tahoma");
        document.getElementById("fontface1").innerHTML = "Tahoma";
    } else if (this.innerHTML == "Times New Roman") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "times new roman");
        document.getElementById("fontface1").innerHTML = "Times New Roman";
    } else if (this.innerHTML == "Trebuchet MS") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "trebuchet ms");
        document.getElementById("fontface1").innerHTML = "Trebuchet MS";
    } else if (this.innerHTML == "Verdana") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontname", false, "verdana");
        document.getElementById("fontface1").innerHTML = "Verdana";
    } else if (this.innerHTML == "1") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "1");
        document.getElementById("fontsize1").innerHTML = "1";
    } else if (this.innerHTML == "2") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "2");
        document.getElementById("fontsize1").innerHTML = "2";
    } else if (this.innerHTML == "3") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "3");
        document.getElementById("fontsize1").innerHTML = "3";
    } else if (this.innerHTML == "4") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "4");
        document.getElementById("fontsize1").innerHTML = "4";
    } else if (this.innerHTML == "5") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "5");
        document.getElementById("fontsize1").innerHTML = "5";
    } else if (this.innerHTML == "6") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "6");
        document.getElementById("fontsize1").innerHTML = "6";
    } else if (this.innerHTML == "7") {
        document.getElementById(rteName).contentWindow.document.execCommand("fontsize", false, "7");
        document.getElementById("fontsize1").innerHTML = "7";
    }
    this.style.color = "#000000";
    this.style.backgroundColor = "#FFFFFF";
    rteHideMenus();
};
function rteMouseOverFormatMenu() {
    document.getElementById("format1").className = "rtedropdown4";
    document.getElementById("format2").className = "rtedropdown5";
};
function rteMouseDownFormatMenu() {
    rteHideMenus();
    document.getElementById("format1").className = "rtedropdown4";
    document.getElementById("format2").className = "rtedropdown6";
    document.getElementById("format1").style.left = rteGetOffsetLeft(document.getElementById("format1"));
    document.getElementById("format1").style.top = rteGetOffsetTop(document.getElementById("format1")) + document.getElementById("format1").offsetHeight;
    document.getElementById("format3").style.display = (document.getElementById("format3").style.display == "none" ) ? "" : "none";
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        if (kids[i].id == "format1" || kids[i].id == "format2") {
            kids[i].onmouseout = rteMouseDownFormatMenu;
        } else if (kids[i].id == "fontface1" || kids[i].id == "fontface2") {
            kids[i].onmouseout = rteMouseOutFontFaceMenu;
        } else if (kids[i].id == "fontsize1" || kids[i].id == "fontsize2") {
            kids[i].onmouseout = rteMouseOutFontSizeMenu;
        } else if (kids[i].id == "fontcolor1" || kids[i].id == "fontcolor2") {
            kids[i].onmouseout = rteMouseOutFontColorMenu;
        }
    }
};
function rteMouseOverFontFaceMenu() {
    document.getElementById("fontface1").className = "rtedropdown4";
    document.getElementById("fontface2").className = "rtedropdown5";
};
function rteMouseDownFontFaceMenu() {
    rteHideMenus();
    document.getElementById("fontface1").className = "rtedropdown4";
    document.getElementById("fontface2").className = "rtedropdown6";
    document.getElementById("fontface1").style.left = rteGetOffsetLeft(document.getElementById("fontface1"));
    document.getElementById("fontface1").style.top = rteGetOffsetTop(document.getElementById("fontface1")) + document.getElementById("fontface1").offsetHeight;
    document.getElementById("fontface3").style.display = (document.getElementById("fontface3").style.display == "none" ) ? "" : "none";
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        if (kids[i].id == "fontface1" || kids[i].id == "fontface2") {
            kids[i].onmouseout = rteMouseDownFontFaceMenu;
        } else if (kids[i].id == "fontsize1" || kids[i].id == "fontsize2") {
            kids[i].onmouseout = rteMouseOutFontSizeMenu;
        } else if (kids[i].id == "fontcolor1" || kids[i].id == "fontcolor2") {
            kids[i].onmouseout = rteMouseOutFontColorMenu;
        }
    }
};
function rteMouseOutFontFaceMenu() {
    document.getElementById("fontface1").className = "rtedropdown1";
    document.getElementById("fontface2").className = "rtedropdown2";
};
function rteMouseOverFontSizeMenu() {
    document.getElementById("fontsize1").className = "rtedropdown4";
    document.getElementById("fontsize2").className = "rtedropdown5";
};
function rteMouseDownFontSizeMenu() {
    rteHideMenus();
    document.getElementById("fontsize1").className = "rtedropdown4";
    document.getElementById("fontsize2").className = "rtedropdown6";
    document.getElementById("fontsize1").style.left = rteGetOffsetLeft(document.getElementById("fontsize1"));
    document.getElementById("fontsize1").style.top = rteGetOffsetTop(document.getElementById("fontsize1")) + document.getElementById("fontsize1").offsetHeight;
    document.getElementById("fontsize3").style.display = (document.getElementById("fontsize3").style.display == "none" ) ? "" : "none";
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        if (kids[i].id == "fontface1" || kids[i].id == "fontface2") {
            kids[i].onmouseout = rteMouseOutFontFaceMenu;
        } else if (kids[i].id == "fontsize1" || kids[i].id == "fontsize2") {
            kids[i].onmouseout = rteMouseDownFontSizeMenu;
        } else if (kids[i].id == "fontcolor1" || kids[i].id == "fontcolor2") {
            kids[i].onmouseout = rteMouseOutFontColorMenu;
        }
    }
};
function rteMouseOutFontSizeMenu() {
    document.getElementById("fontsize1").className = "rtedropdown1";
    document.getElementById("fontsize2").className = "rtedropdown2";
};
function rteMouseOverFontColorMenu() {
    document.getElementById("fontcolor1").className = "rtedropdown8";
    document.getElementById("fontcolor2").className = "rtedropdown5b";
};
function rteMouseDownFontColorMenu() {
    rteHideMenus();
    document.getElementById("fontcolor1").className = "rtedropdown12";
    document.getElementById("fontcolor2").className = "rtedropdown9b";
    document.getElementById("fontcolor1").style.left = rteGetOffsetLeft(document.getElementById("fontcolor1"));
    document.getElementById("fontcolor1").style.top = rteGetOffsetTop(document.getElementById("fontcolor1")) + document.getElementById("fontcolor1").offsetHeight;
    document.getElementById("fontcolor3").style.display = (document.getElementById("fontcolor3").style.display == "none" ) ? "" : "none";
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        if (kids[i].id == "fontface1" || kids[i].id == "fontface2") {
            kids[i].onmouseout = rteMouseOutFontFaceMenu;
        } else if (kids[i].id == "fontsize1" || kids[i].id == "fontsize2") {
            kids[i].onmouseout = rteMouseOutFontSizeMenu;
        } else if (kids[i].id == "fontcolor1" || kids[i].id == "fontcolor2") {
            kids[i].onmouseout = rteMouseDownFontColorMenu;
        }
    }
};
function rteMouseOutFontColorMenu() {
    document.getElementById("fontcolor1").className = "rtedropdown10";
    document.getElementById("fontcolor2").className = "rtedropdown11b";
};
function rteBtnMouseUpBottom() {
    this.className = "rtebtn9";
};
function rteBtnMouseOutBottom() {
    this.className = "rtebtn6";
};
function rteBtnMouseOutDownBottom() {
    this.className = "rtebtn9";
};
function rteBtnMouseOverBottom() {
    this.className = "rtebtn7";
};
function rteBtnMouseDownBottom() {
    var kids = document.getElementsByTagName("DIV");
    for (var i=0; i < kids.length; i++) {
        if(kids[i].className == "rtebtn6" || kids[i].className == "rtebtn7" || kids[i].className == "rtebtn8" || kids[i].className == "rtebtn9"){
            kids[i].className = "rtebtn6";
            kids[i].onmouseover = rteBtnMouseOverBottom;
            kids[i].onmouseout = rteBtnMouseOutBottom;
            kids[i].onmousedown = rteBtnMouseDownBottom;
            kids[i].onmouseup = rteBtnMouseUpBottom;
        }
    }
    this.className = "rtebtn9";
    this.onmouseover = rteBtnMouseOverBottom;
    this.onmouseout = rteBtnMouseOutDownBottom;
    this.onmouseup = rteBtnMouseUpBottom;
};
function rteBtnMouseDown() {
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        if (kids[i].className == "rtebtn2" || kids[i].className == "rtebtn3" || kids[i].className == "rtebtn4") {
            kids[i].className = "rtebtn1";
        }
    }
    rteSelection();
    this.className = "rtebtn4";
};
function rteBtnMouseUp() {
    this.className = "rtebtn4";
};
function rteBtnMouseOut() {
    this.className = "rtebtn1";
};
function rteBtnMouseOver() {
    this.className = "rtebtn2";
};
function rteAction(ID) {
    rteHideMenus();
    if (this.id != "editlink" && this.id != "insertflash"&& this.id != "edittable" && this.id != "createlink" && this.id != "insertimage" && this.id != "inserttable" && this.id != "insertrowbelow" && this.id != "insertrowabove" && this.id != "insertcolumnleft" && this.id != "insertcolumnright" && this.id != "deletecolumn" && this.id != "deleterow" && this.id != "insertform" && this.id != "form_checkbox" && this.id != "form_radio" && this.id != "form_dropdown" && this.id != "form_textarea" && this.id != "form_submit" && this.id != "form_image_submit" && this.id != "form_reset" && this.id != "form_hidden" && this.id != "form_password" && this.id != "form_textfield" && this.id != "spellcheck" && this.id != "printrte" && this.id != "aboutrte") {
        document.getElementById(rteName).contentWindow.document.execCommand(this.id, false, null);
        document.getElementById(this.id).className = "rtebtn4";
        document.getElementById(this.id).onmouseout = rteBtnMouseDown;
        document.getElementById(rteName).contentWindow.focus();
    }
};
function startRTE(rtePreloadContent) {
    rteCSS = document.getElementById("preview_css").value;
    document.getElementById(rteName).contentWindow.document.designMode = "on";
    document.getElementById(rteFormName).value = rtePreloadContent;
    document.getElementById(rteName).contentWindow.document.open();
    document.getElementById(rteName).contentWindow.document.write("<html>");
    document.getElementById(rteName).contentWindow.document.write("  <head>");
    document.getElementById(rteName).contentWindow.document.write("    <style type=\"text/css\">@import url("+rteCSS+");</style>");
    document.getElementById(rteName).contentWindow.document.write("  </head>");
    document.getElementById(rteName).contentWindow.document.write("  <body id=\"textBody\" class=\"textBody\" style=\"overflow-y: scroll; height:101%\">");
    document.getElementById(rteName).contentWindow.document.write(      rtePreloadContent);
    document.getElementById(rteName).contentWindow.document.write("  </body>")
    document.getElementById(rteName).contentWindow.document.write("</html>");
    document.getElementById(rteName).contentWindow.document.close();
    if (document.all && !window.opera) {
        document.getElementById(rteName).contentWindow.document.attachEvent("onkeypress", rteSelection);
        document.getElementById(rteName).contentWindow.document.attachEvent("onclick", rteSelection);
        document.getElementById(rteName).contentWindow.document.attachEvent("onmouseup", rteSelection);
    } else {
        document.getElementById(rteName).contentWindow.document.execCommand("useCSS", false, null);
        document.getElementById(rteName).contentWindow.document.addEventListener("keypress", rteSelection, true);
        document.getElementById(rteName).contentWindow.document.addEventListener("click", rteSelection, true);
        document.getElementById(rteName).contentWindow.document.addEventListener("mouseup", rteSelection, true);
    }
    rteSelection();
    var kids = document.getElementsByTagName("DIV");
    for (var i=0; i < kids.length; i++) {
        if(kids[i].className == "rtebtn6"){
            kids[i].onmouseover = rteBtnMouseOverBottom;
            kids[i].onmouseout = rteBtnMouseOutBottom;
            kids[i].onmousedown = rteBtnMouseDownBottom;
            kids[i].onmouseup = rteBtnMouseUpBottom;
        }
    }
};
function rteSelection() {
    rteHideMenus();
    //to do analize
    document.getElementById("fontface1").innerHTML = "Verdana";
    document.getElementById("fontsize1").innerHTML = "2";
    var kids = document.getElementsByTagName('DIV');
    for (var i=0; i < kids.length; i++) {
        switch(kids[i].className)

        {
            case "rtebtn1":
                if (kids[i].onmouseover != rteBtnMouseOver)
                {
                    kids[i].onmouseover = rteBtnMouseOver;
                }
                if (kids[i].onclick != rteAction)
                {
                    kids[i].onclick = rteAction;
                }
                if (kids[i].className == "rtebtn4") {
                    if (kids[i].onmouseout != rteBtnMouseDown)
                    {
                        kids[i].onmouseout = rteBtnMouseDown;
                    }
                } else {
                    if (kids[i].onmouseout != rteBtnMouseOut)
                    {
                        kids[i].onmouseout = rteBtnMouseOut;
                    }
                }
                if (kids[i].onmousedown != rteBtnMouseDown)
                {
                    kids[i].onmousedown = rteBtnMouseDown;
                }
                if (kids[i].onmouseup != rteBtnMouseUp)
                {
                    kids[i].onmouseup = rteBtnMouseUp;
                }
                break;
            case "rtebtn2":
                kids[i].className = "rtebtn1";
                if (kids[i].onmouseover != rteBtnMouseOver)
                {
                    kids[i].onmouseover = rteBtnMouseOver;
                }
                if (kids[i].onclick != rteAction)
                {
                    kids[i].onclick = rteAction;
                }
                if (kids[i].className == "rtebtn4") {
                    if (kids[i].onmouseout = rteBtnMouseDown)
                    {
                        kids[i].onmouseout = rteBtnMouseDown;
                    }
                } else {
                    if (kids[i].onmouseout != rteBtnMouseOut)
                    {
                        kids[i].onmouseout = rteBtnMouseOut;
                    }
                }
                if (kids[i].onmousedown != rteBtnMouseDown)
                {
                    kids[i].onmousedown = rteBtnMouseDown;
                }
                if (kids[i].onmouseup != rteBtnMouseUp)
                {
                    kids[i].onmouseup = rteBtnMouseUp;
                }
                break;
            case "rtebtn3":
                kids[i].className = "rtebtn1";
                if (kids[i].onmouseover != rteBtnMouseOver)

                {
                    kids[i].onmouseover = rteBtnMouseOver;
                }
                if (kids[i].onclick != rteAction)
                {
                    kids[i].onclick = rteAction;
                }
                if (kids[i].className == "rtebtn4") {
                    if (kids[i].onmouseout != rteBtnMouseDown)
                    {
                        kids[i].onmouseout = rteBtnMouseDown;
                    }
                } else {
                    if (kids[i].onmouseout != rteBtnMouseOut)
                    {
                        kids[i].onmouseout = rteBtnMouseOut;
                    }
                }
                if (kids[i].onmousedown != rteBtnMouseDown)
                {
                    kids[i].onmousedown = rteBtnMouseDown;
                }
                if (kids[i].onmouseup != rteBtnMouseUp)
                {
                    kids[i].onmouseup = rteBtnMouseUp;
                }
                break;
            case "rtebtn4":
                kids[i].className = "rtebtn1";
                if (kids[i].onmouseover != rteBtnMouseOver)
                {
                    kids[i].onmouseover = rteBtnMouseOver;
                }
                if (kids[i].onclick != rteAction)
                {
                    kids[i].onclick = rteAction;
                }
                if (kids[i].onmouseout != rteBtnMouseOut)
                {
                    kids[i].onmouseout = rteBtnMouseOut;
                }
                if (kids[i].onmousedown != rteBtnMouseDown)
                {
                    kids[i].onmousedown = rteBtnMouseDown;
                }
                if (kids[i].onmouseup != rteBtnMouseUp)
                {
                    kids[i].onmouseup = rteBtnMouseUp;
                }
                break;
        }
        switch(kids[i].id)
        {
            case "fontface1":
                if (kids[i].onmouseover != rteMouseOverFontFaceMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontFaceMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontFaceMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontFaceMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontFaceMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontFaceMenu;
                }
                if (kids[i].className == "rtedropdown6") {
                    if (kids[i].onmouseout != rteMouseDownFontFaceMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontFaceMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontFaceMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontFaceMenu;
                    }
                }
                break;
            case "fontface2":
                if (kids[i].onmouseover != rteMouseOverFontFaceMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontFaceMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontFaceMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontFaceMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontFaceMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontFaceMenu;
                }
                if (kids[i].className == "rtedropdown6") {
                    if (kids[i].onmouseout != rteMouseDownFontFaceMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontFaceMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontFaceMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontFaceMenu;
                    }
                }
                break;
            case "fontsize1":
                if (kids[i].onmouseover != rteMouseOverFontSizeMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontSizeMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontSizeMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontSizeMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontSizeMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontSizeMenu;
                }
                if (kids[i].className == "rtedropdown6") {
                    if (kids[i].onmouseout != rteMouseDownFontSizeMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontSizeMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontSizeMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontSizeMenu;
                    }
                }
                break;
            case "fontsize2":
                if (kids[i].onmouseover != rteMouseOverFontSizeMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontSizeMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontSizeMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontSizeMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontSizeMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontSizeMenu;
                }
                if (kids[i].className == "rtedropdown6") {
                    if (kids[i].onmouseout != rteMouseDownFontSizeMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontSizeMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontSizeMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontSizeMenu;
                    }
                }
                break;
            case "fontcolor1":
                if (kids[i].onmouseover != rteMouseOverFontColorMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontColorMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontColorMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontColorMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontColorMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontColorMenu;
                }
                if (kids[i].className == "rtedropdown9") {
                    if (kids[i].onmouseout != rteMouseDownFontColorMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontColorMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontColorMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontColorMenu;
                    }
                }
                break;
            case "fontcolor2":
                if (kids[i].onmouseover != rteMouseOverFontColorMenu)
                {
                    kids[i].onmouseover = rteMouseOverFontColorMenu;
                }
                if (kids[i].onmousedown != rteMouseDownFontColorMenu)
                {
                    kids[i].onmousedown = rteMouseDownFontColorMenu;
                }
                if (kids[i].onmouseout != rteMouseOutFontColorMenu)
                {
                    kids[i].onmouseout = rteMouseOutFontColorMenu;
                }
                if (kids[i].className == "rtedropdown9") {
                    if (kids[i].onmouseout != rteMouseDownFontColorMenu)
                    {
                        kids[i].onmouseout = rteMouseDownFontColorMenu;
                    }
                } else {
                    if (kids[i].onmouseout != rteMouseOutFontColorMenu)
                    {
                        kids[i].onmouseout = rteMouseOutFontColorMenu;
                    }
                }
                break;
            case "formatblock":
                if (kids[i].onmouseover != rteMouseOverMenuContents)
                {
                    kids[i].onmouseover = rteMouseOverMenuContents;
                }
                if (kids[i].onmouseout != rteMouseOutMenuContents)
                {
                    kids[i].onmouseout = rteMouseOutMenuContents;
                }
                if (kids[i].onmousedown != rteMouseDownMenuContents)
                {
                    kids[i].onmousedown = rteMouseDownMenuContents;
                }
                break;
            case "fontface":
                if (kids[i].onmouseover != rteMouseOverMenuContents)
                {
                    kids[i].onmouseover = rteMouseOverMenuContents;
                }
                if (kids[i].onmouseout != rteMouseOutMenuContents)
                {
                    kids[i].onmouseout = rteMouseOutMenuContents;
                }
                if (kids[i].onmousedown != rteMouseDownMenuContents)
                {
                    kids[i].onmousedown = rteMouseDownMenuContents;
                }
                break;
            case "fontsize":
                if (kids[i].onmouseover != rteMouseOverMenuContents)
                {
                    kids[i].onmouseover = rteMouseOverMenuContents;
                }
                if (kids[i].onmouseout != rteMouseOutMenuContents)
                {
                    kids[i].onmouseout = rteMouseOutMenuContents;
                }
                if (kids[i].onmousedown != rteMouseDownMenuContents)
                {
                    kids[i].onmousedown = rteMouseDownMenuContents;
                }
                break;
            case "fontcolor":
                if (kids[i].onmouseover != rteMouseOverMenuFontColorContents)
                {
                    kids[i].onmouseover = rteMouseOverMenuFontColorContents;
                }
                if (kids[i].onmouseout != rteMouseOutMenuFontColorContents)
                {
                    kids[i].onmouseout = rteMouseOutMenuFontColorContents;
                }
                break;
        }
    }
    var tbl = false;
    var in_list = false;
    if (window.getSelection)
    {
        var selected_obj = document.getElementById(rteName).contentWindow.window.getSelection().focusNode;
    }
    else if (document.getSelection)
    {
        var selected_obj = document.getElementById(rteName).contentWindow.document.getSelection().focusNode;
    }
    else if (document.selection)
    {
        var selected_obj = document.getElementById(rteName).contentWindow.document.selection.createRange().parentElement();
    }
    var is_link = false;
    var is_table = false;
    var current_tag = selected_obj;
    var textcolor = "";
    document.getElementById("fontcolor4").style.backgroundColor = "#000000";
    if($(selected_obj).length != 0){
        var previous_tagName = selected_obj.tagName;
        while(previous_tagName != "HTML"){
            if(previous_tagName == "B" || previous_tagName == "STRONG"){
                document.getElementById("bold").className = "rtebtn4";
                document.getElementById("bold").onmouseout = rteBtnMouseDown;
            }
            if (previous_tagName == "LI") {
                in_list = true;
            }
            if (previous_tagName == "TD" && !is_table) {
                is_table = true;
            }
            if(previous_tagName == "I" || previous_tagName == "EM"){
                document.getElementById("italic").className = "rtebtn4";
                document.getElementById("italic").onmouseout = rteBtnMouseDown;
            }
            if(previous_tagName == "U"){
                document.getElementById("underline").className = "rtebtn4";
                document.getElementById("underline").onmouseout = rteBtnMouseDown;
            }
            if(previous_tagName == "A"){
                is_link = true;
                document.getElementById("editlink").className = "rtebtn4";
                document.getElementById("editlink").onmouseout = rteBtnMouseDown;
            }
            if(previous_tagName == "UL"){
                document.getElementById("insertunorderedlist").className = "rtebtn4";
                document.getElementById("insertunorderedlist").onmouseout = rteBtnMouseDown;
            }
            if(previous_tagName == "OL"){
                document.getElementById("insertorderedlist").className = "rtebtn4";
                document.getElementById("insertorderedlist").onmouseout = rteBtnMouseDown;
            }
            if(previous_tagName == "BLOCKQUOTE"){
                document.getElementById("indent").className = "rtebtn4";
                document.getElementById("indent").onmouseout = rteBtnMouseDown;
            }
            if (current_tag.align == "left") {
                document.getElementById("justifyleft").className = "rtebtn4";
                document.getElementById("justifyleft").onmouseout = rteBtnMouseDown;
            } else if (current_tag.align == "center") {
                document.getElementById("justifycenter").className = "rtebtn4";
                document.getElementById("justifycenter").onmouseout = rteBtnMouseDown;
            } else if (current_tag.align == "right") {
                document.getElementById("justifyright").className = "rtebtn4";
                document.getElementById("justifyright").onmouseout = rteBtnMouseDown;
            } else if (current_tag.align == "justify") {
                document.getElementById("justifyfull").className = "rtebtn4";
                document.getElementById("justifyfull").onmouseout = rteBtnMouseDown;
            } else if (current_tag.align == "") {
                document.getElementById("justifyleft").className = "rtebtn1";
            } else { }
            if (current_tag.face == "arial") {
                document.getElementById("fontface1").innerHTML = "Arial";
            } else if (current_tag.face == "arial black") {
                document.getElementById("fontface1").innerHTML = "Arial Black";
            } else if (current_tag.face == "arial narrow") {
                document.getElementById("fontface1").innerHTML = "Arial Narrow";
            } else if (current_tag.face == "courier new") {
                document.getElementById("fontface1").innerHTML = "Courier New";
            } else if (current_tag.face == "century gothic") {
                document.getElementById("fontface1").innerHTML = "Century Gothic";
            } else if (current_tag.face == "comic sans ms") {
                document.getElementById("fontface1").innerHTML = "Comic Sans MS";
            } else if (current_tag.face == "impact") {
                document.getElementById("fontface1").innerHTML = "Impact";
            } else if (current_tag.face == "tahoma") {
                document.getElementById("fontface1").innerHTML = "Tahoma";
            } else if (current_tag.face == "times new roman") {
                document.getElementById("fontface1").innerHTML = "Times New Roman";
            } else if (current_tag.face == "trebuchet ms") {
                document.getElementById("fontface1").innerHTML = "Trebuchet MS";
            } else if (current_tag.face == "verdana") {
                document.getElementById("fontface1").innerHTML = "Verdana";
            }
            if (current_tag.size == "1") {
                document.getElementById("fontsize1").innerHTML = "1";
            } else if (current_tag.size == "2") {
                document.getElementById("fontsize1").innerHTML = "2";
            } else if (current_tag.size == "3") {
                document.getElementById("fontsize1").innerHTML = "3";
            } else if (current_tag.size == "4") {
                document.getElementById("fontsize1").innerHTML = "4";
            } else if (current_tag.size == "5") {
                document.getElementById("fontsize1").innerHTML = "5";
            } else if (current_tag.size == "6") {
                document.getElementById("fontsize1").innerHTML = "6";
            } else if (current_tag.size == "7") {
                document.getElementById("fontsize1").innerHTML = "7";
            } else { }
            if (current_tag.color != null) {
                textcolor = current_tag.color;
            }
            current_tag = current_tag.parentNode;
            previous_tagName = current_tag.tagName;
        }
    }
    if (textcolor == "") {
        textcolor = "#000000";
    }
    document.getElementById("fontcolor4").style.backgroundColor = textcolor;
};
function menuBuilder() {
    if (rteFontFace) {
        document.getElementById("rtefontface").style.display = "";
    } else {
        document.getElementById("rtefontface").style.display = "none";
    }
    if (rteFontSize) {
        document.getElementById("rtefontsize").style.display = "";
    } else {
        document.getElementById("rtefontsize").style.display = "none";
    }
    if (rteFontColor) {
        document.getElementById("rtefontcolor").style.display = "";
    } else {
        document.getElementById("rtefontcolor").style.display = "none";
    }
    if (rteBold) {
        document.getElementById("bold").style.display = "";
    } else {
        document.getElementById("bold").style.display = "none";
    }
    if (rteItalic) {
        document.getElementById("italic").style.display = "";
    } else {
        document.getElementById("italic").style.display = "none";
    }
    if (rteUnderline) {
        document.getElementById("underline").style.display = "";
    } else {
        document.getElementById("underline").style.display = "none";
    }
    if (rteLeftAlign) {
        document.getElementById("justifyleft").style.display = "";
    } else {
        document.getElementById("justifyleft").style.display = "none";
    }
    if (rteCenterAlign) {
        document.getElementById("justifycenter").style.display = "";
    } else {
        document.getElementById("justifycenter").style.display = "none";
    }
    if (rteRightAlign) {
        document.getElementById("justifyright").style.display = "";
    } else {
        document.getElementById("justifyright").style.display = "none";
    }
    if (rteFullAlign) {
        document.getElementById("justifyfull").style.display = "";
    } else {
        document.getElementById("justifyfull").style.display = "none";
    }
    if (rteHorizontalRule) {
        document.getElementById("inserthorizontalrule").style.display = "";
    } else {
        document.getElementById("inserthorizontalrule").style.display = "none";
    }
	if (!rteFontFace && !rteFontSize && !rteFontColor) {
        document.getElementById("rtesep1").style.display = "none";
        document.getElementById("rtesep2").style.display = "none";
    }
    if (!rteBold && !rteItalic && !rteUnderline ) {
        document.getElementById("rtesep3").style.display = "none";
    }
    if (!rteLeftAlign && !rteCenterAlign && !rteRightAlign && !rteFullAlign && !rteHorizontalRule) {
        document.getElementById("rtesep4").style.display = "none";
    }
	if (!rteFontFace && !rteFontSize && !rteBold && !rteItalic && !rteUnderline && !rteLeftAlign && !rteCenterAlign && !rteRightAlign && !rteFullAlign && !rteHorizontalRule && !rteSuperscript && !rteSubscript) {

        document.getElementById("tb1").style.display = "none";
    }
};
function initRTE(rtePreloadContent, rteCSS) {
    if (!document.designMode) {
        document.write('<textarea id="' + rteFormName + '" name="' + rteFormName + '" style="width:' + rteWidth + ';height:' + rteHeight + 'px;"></textarea>');
    } else {
        document.write('<style>');
        document.write('.rtebg {');
        document.write('	background-image:url(' + rteImagePath + 'bg.gif);');
        document.write('	font-family:Arial, Helvetica, sans-serif;');
        document.write('	font-size:10px;');
        document.write('}');
        document.write('.rtedropdown1 {');
        document.write('	height:16px;');
        document.write('	font-family:Arial, Helvetica, sans-serif;');
        document.write('	padding-left:3px;');
        document.write('	padding-right:3px;');
        document.write('	font-size:11px;');
        document.write('	border:1px solid #FFFFFF;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown2 {');
        document.write('	width:11px;');
        document.write('	background-image:url(' + rteImagePath + 'bg.gif);');
        document.write('	border-top:1px solid #FFFFFF;');
        document.write('	border-right:1px solid #FFFFFF;');
        document.write('	border-bottom:1px solid #FFFFFF;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	height:20px;');
        }
        else
        {
            document.write('	height:16px;');
        }
        document.write('}');
        document.write('.rtedropdown4 {');
        document.write('	font-family:Arial, Helvetica, sans-serif;');
        document.write('	padding-left:3px;');
        document.write('	padding-right:3px;');
        document.write('	font-size:11px;');
        document.write('	border:1px solid #002D96;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	line-height:18px;');
        }
        else
        {
            document.write('	height:16px;');
        }
        document.write('}');
        document.write('.rtedropdown5 {');
        document.write('	background-image:url(' + rteImagePath + 'bgover.gif);');
        document.write('	border-top:1px solid #000080;');
        document.write('	border-right:1px solid #000080;');
        document.write('	border-bottom:1px solid #000080;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	height:20px;');
        }
        else
        {
            document.write('	height:16px;');
        }
        document.write('}');
        document.write('.rtedropdown5b {');
        document.write('	background-image:url(' + rteImagePath + 'bgover.gif);');
        document.write('	border-top:1px solid #000080;');
        document.write('	border-right:1px solid #000080;');
        document.write('	border-bottom:1px solid #000080;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	height:21px;');
        }
        else
        {
            document.write('	height:19px;');
        }
        document.write('}');
        document.write('.rtedropdown6 {');
        document.write('	width:11px;');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('	border-top:1px solid #000080;');
        document.write('	border-right:1px solid #000080;');
        document.write('	border-bottom:1px solid #000080;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	height:20px;');
        }
        else
        {
            document.write('	height:16px;');
        }
        document.write('}');
        document.write('.rtedropdown7 {');
        document.write('	border:1px solid #002D96;');
        document.write('	background-color:#FFFFFF;');
        document.write('	font-family:Arial, Helvetica, sans-serif;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown8 {');
        document.write('	background-image:url(' + rteImagePath + 'bgover.gif);');
        document.write('	border:1px solid #002D96;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown9 {');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('	border-top:1px solid #000080;');
        document.write('	border-right:1px solid #000080;');
        document.write('	border-bottom:1px solid #000080;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown9b {');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('	border-top:1px solid #000080;');
        document.write('	border-right:1px solid #000080;');
        document.write('	border-bottom:1px solid #000080;');
        document.write('	cursor:default;');
        if (document.all && !window.opera)
        {
            document.write('	height:21px;');
        }
        else
        {
            document.write('	height:19px;');
        }
        document.write('}');
        document.write('.rtedropdown10 {');
        document.write('	border:0px solid transparent;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown11 {');
        document.write('	border-top:0px solid transparent;');
        document.write('	border-right:0px solid transparent;');
        document.write('	border-bottom:0px solid transparent;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown11b {');
        document.write('	border-top:0px solid transparent;');
        document.write('	border-right:0px solid transparent;');
        document.write('	border-bottom:0px solid transparent;');
        document.write('	cursor:default;');
        document.write('	height:19px;');
        document.write('}');
        document.write('.rtedropdown12 {');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('	border:1px solid #000080;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtedropdown13 {');
        document.write('	padding:1px;');
        document.write('	border:1px solid #FFFFFF;');
        document.write('	background-color:#FFFFFF;');
        document.write('}');
        document.write('.rtedropdown14 {');
        document.write('	padding:1px;');
        document.write('	border:1px solid #000080;');
        document.write('	background-color:#FFEEC2;');
        document.write('}');
        document.write('.rtebtn1 {');
        document.write('	display:block;');
        document.write('	width:21px;');
        document.write('	height:20px;');
        document.write('	padding: 1px;');
        document.write('	background-image:url(' + rteImagePath + 'bg.gif);');
        document.write('}');
        document.write('.rtebtn2 {');
        document.write('	display:block;');
        document.write('	width:21px;');
        document.write('	height:20px;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgover.gif);');
        document.write('}');
        document.write('.rtebtn3 {');
        document.write('	display:block;');
        document.write('	width:21px;');
        document.write('	height:20px;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgselect.gif);');
        document.write('}');
        document.write('.rtebtn4 {');
        document.write('	display:block;');
        document.write('	width:21px;');
        document.write('	height:20px;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('}');
        document.write('.rtebtn5 {');
        document.write('	display:block;');
        document.write('	width:21px;');
        document.write('	height:20px;');
        document.write('	padding: 1px;');
        document.write('	background-image:url(' + rteImagePath + 'bg.gif);');
        document.write('}');
        document.write('.rtebtn6 {');
        document.write('	display:block;');
        document.write('	padding: 3px;');
        document.write('	cursor:default;');
        document.write('}');
        document.write('.rtebtn7 {');
        document.write('	display:block;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgover.gif);');
        document.write('	cursor:default;');
        document.write('	padding: 2px;');
        document.write('}');
        document.write('.rtebtn8 {');
        document.write('	display:block;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgselect.gif);');
        document.write('	cursor:default;');
        document.write('	padding: 2px;');
        document.write('}');
        document.write('.rtebtn9 {');
        document.write('	display:block;');
        document.write('	border:1px solid #000080;');
        document.write('	background-image:url(' + rteImagePath + 'bgdown.gif);');
        document.write('	cursor:default;');
        document.write('	padding: 2px;');
        document.write('}');
        document.write('</style>');
        document.write('<input type="hidden" id="preview_css" value="' + rteCSS + '">');
        document.write('<input type="hidden" id="iframe_name" value="' + rteName + '">');
        document.write('<table style="width:' + rteWidth + ';border-left:1px solid #3B619C;border-right:1px solid #3B619C;border-top:1px solid #3B619C;" cellpadding="0" cellspacing="0">');
        document.write('  <tr>');
        document.write('    <td bgcolor="#C3DAF9">');
        document.write('	<table cellpadding="0" cellspacing="0" id="tb1" onmousedown="return false;">');
        document.write('      <tr>');
        document.write('        <td width="7"><img src="' + rteImagePath + 'start.gif" width="7" height="25"></td>');
        document.write('        <td class="rtebg" id="rtesep1"><img src="' + rteImagePath + 'blank.gif"></td>');
        document.write('        <td class="rtebg"><img src="' + rteImagePath + 'blank.gif"></td>');
        document.write('        <td background="' + rteImagePath + 'bg.gif" id="rtefontface">');
        document.write('		<table style="width:110px;" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" title="Font">');
        document.write('          <tr>');
        document.write('            <td nowrap><div unselectable="on" id="fontface1" class="rtedropdown1" style="width:98px;font-family:arial;font-size:11px;color:#000000;">Verdana</div></td>');
        document.write('            <td><div unselectable="on" id="fontface2" class="rtedropdown2"><img src="' + rteImagePath + 'arrow.gif" width="11" height="16"></div></td>');
        document.write('          </tr>');
        document.write('        </table>');
        document.write('		<div id="fontface3" class="rtedropdown7" style="position:absolute;display:none;">');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:arial;padding:5px;" nowrap>Arial</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:arial black;padding:5px;" nowrap>Arial Black</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:arial narrow;padding:5px;" nowrap>Arial Narrow</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:courier new;padding:5px;" nowrap>Courier New</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:century gothic;padding:5px;" nowrap>Century Gothic</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:comic sans ms;padding:5px;" nowrap>Comic Sans MS</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:impact;padding:5px;" nowrap>Impact</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:tahoma;padding:5px;" nowrap>Tahoma</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:times new roman;padding:5px;" nowrap>Times New Roman</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:trebuchet ms;padding:5px;" nowrap>Trebuchet MS</div>');
        document.write('			<div unselectable="on" id="fontface" style="color:#000000;font-family:verdana;padding:5px;" nowrap>Verdana</div>');
        document.write('		</div>');
        document.write('        </td>');
        document.write('        <td class="rtebg"><img src="' + rteImagePath + 'blank.gif"></td>');
        document.write('        <td background="' + rteImagePath + 'bg.gif" id="rtefontsize">');
        document.write('		<table width="100%" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF" title="Font Size">');
        document.write('          <tr>');
        document.write('            <td><div unselectable="on" id="fontsize1" class="rtedropdown1" style="font-family:arial;font-size:11px;color:#000000;">2</div></td>');
        document.write('            <td><div unselectable="on" id="fontsize2" class="rtedropdown2"><img src="' + rteImagePath + 'arrow.gif" width="11" height="16"></div></td>');
        document.write('          </tr>');
        document.write('        </table>');
        document.write('		<div id="fontsize3" class="rtedropdown7" style="position:absolute;display:none;">');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:7px;padding:5px;">1</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:10px;padding:5px;">2</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:12px;padding:5px;">3</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:13px;padding:5px;">4</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:17px;padding:5px;">5</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:23px;padding:5px;">6</div>');
        document.write('			<div unselectable="on" id="fontface" style="font-family:arial;color:#000000;font-size:35px;padding:5px;">7</div>');
        document.write('		</div>');
        document.write('        </td>');
        document.write('        <td class="rtebg"><img src="' + rteImagePath + 'blank.gif"></td>');
        document.write('        <td class="rtebg" id="rtefontcolor" title="Font Color">');
        document.write('        	<table style="width:35px;" border="0" cellspacing="0" cellpadding="0">');
        document.write('        		<tr>');
        if (!document.all || window.opera)
        {
            document.write('        			<td><div id="fontcolor1" align="center" class="rtedropdown5" style="padding-bottom:1px;"><img src="' + rteImagePath + 'fontcolor.gif"><br/><img id="fontcolor4" src="' + rteImagePath + 'fontcolor2.gif" style="background-color:#FF0000;"></div></td>');
            document.write('        			<td><div align="center" id="fontcolor2" class="rtedropdown8"><img src="' + rteImagePath + 'arrow.gif"></div></td>');
        }
        else
        {
            document.write('        			<td><div id="fontcolor1" align="center" class="rtedropdown5" style="padding-bottom:1px;"><img src="' + rteImagePath + 'fontcolor.gif"><br/><img id="fontcolor4" src="' + rteImagePath + 'fontcolor2.gif" style="background-color:#FF0000;"></div></td>');
            document.write('        			<td><div align="center" id="fontcolor2" class="rtedropdown8"><img src="' + rteImagePath + 'arrow.gif"></div></td>');
        }
        document.write('        		</tr>');
        document.write('        	</table>');
        document.write('		<div id="fontcolor3" class="rtedropdown7" style="position:absolute;display:none;padding:4px;border:1px solid #002D96;">');
        document.write('			<table border="0" cellspacing="1" cellpadding="0">');
        document.write('				<tr>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#000000;" onClick="rteColorClick(\'#000000\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#993300;" onClick="rteColorClick(\'#993300\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#333300;" onClick="rteColorClick(\'#333300\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#003300;" onClick="rteColorClick(\'#003300\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#003366;" onClick="rteColorClick(\'#003366\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#000080;" onClick="rteColorClick(\'#000080\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#333399;" onClick="rteColorClick(\'#333399\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#333333;" onClick="rteColorClick(\'#333333\');"></div></td>');
        document.write('				</tr>');
        document.write('				<tr>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#800000;" onClick="rteColorClick(\'#800000\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FF6600;" onClick="rteColorClick(\'#FF6600\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#808000;" onClick="rteColorClick(\'#808000\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#008000;" onClick="rteColorClick(\'#008000\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#008080;" onClick="rteColorClick(\'#008080\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#0000FF;" onClick="rteColorClick(\'#0000FF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#666699;" onClick="rteColorClick(\'#666699\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#808080;" onClick="rteColorClick(\'#808080\');"></div></td>');
        document.write('				</tr>');
        document.write('				<tr>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FF0000;" onClick="rteColorClick(\'#FF0000\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FF9900;" onClick="rteColorClick(\'#FF9900\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#99CC00;" onClick="rteColorClick(\'#99CC00\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#339966;" onClick="rteColorClick(\'#339966\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#33CCCC;" onClick="rteColorClick(\'#33CCCC\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#3366FF;" onClick="rteColorClick(\'#3366FF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#800080;" onClick="rteColorClick(\'#800080\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#999999;" onClick="rteColorClick(\'#999999\');"></div></td>');
        document.write('				</tr>');
        document.write('				<tr>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FF00FF;" onClick="rteColorClick(\'#FF00FF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FFCC00;" onClick="rteColorClick(\'#FFCC00\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FFFF00;" onClick="rteColorClick(\'#FFFF00\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#00FF00;" onClick="rteColorClick(\'#00FF00\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#00FFFF;" onClick="rteColorClick(\'#00FFFF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#00CCFF;" onClick="rteColorClick(\'#00CCFF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#993366;" onClick="rteColorClick(\'#993366\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#C0C0C0;" onClick="rteColorClick(\'#C0C0C0\');"></div></td>');
        document.write('				</tr>');
        document.write('				<tr>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FF99CC;" onClick="rteColorClick(\'#FF99CC\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FFCC99;" onClick="rteColorClick(\'#FFCC99\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FFFF99;" onClick="rteColorClick(\'#FFFF99\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#CCFFCC;" onClick="rteColorClick(\'#CCFFCC\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#CCFFFF;" onClick="rteColorClick(\'#CCFFFF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#99CCFF;" onClick="rteColorClick(\'#99CCFF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#CC99FF;" onClick="rteColorClick(\'#CC99FF\');"></div></td>');
        document.write('					<td><div id="fontcolor" class="rtedropdown13"><img src="' + rteImagePath + 'fontcolor3.gif" style="background-color:#FFFFFF;" onClick="rteColorClick(\'#FFFFFF\');"></div></td>');
        document.write('				</tr>');
        document.write('			</table>');
        document.write('		</div>');
        document.write('        </td>');
        document.write('        <td class="rtebg" id="rtesep2"><img src="' + rteImagePath + 'seperator.gif"></td>');
        document.write('        <td class="rtebg" title="Bold"><div id="bold" class="rtebtn1"><img src="' + rteImagePath + 'bold.gif"></div></td>');
        document.write('        <td class="rtebg" title="Italic"><div id="italic" class="rtebtn1"><img src="' + rteImagePath + 'italic.gif"></div></td>');
        document.write('        <td class="rtebg" title="Underline"><div id="underline" class="rtebtn1"><img src="' + rteImagePath + 'underline.gif"></div></td>');
        document.write('        <td class="rtebg" id="rtesep3"><img src="' + rteImagePath + 'seperator.gif"></td>');
        document.write('        <td class="rtebg" title="Align Left"><div id="justifyleft" class="rtebtn1"><img src="' + rteImagePath + 'leftalign.gif" width="21" height="20"></div></td>');
        document.write('        <td class="rtebg" title="Center"><div id="justifycenter" class="rtebtn1"><img src="' + rteImagePath + 'centeralign.gif"></div></td>');
        document.write('        <td class="rtebg" title="Align Right"><div id="justifyright" class="rtebtn1"><img src="' + rteImagePath + 'rightalign.gif"></div></td>');
        document.write('        <td class="rtebg" title="Justify"><div id="justifyfull" class="rtebtn1"><img src="' + rteImagePath + 'fullalign.gif"></div></td>');
        document.write('        <td class="rtebg" title="Horizontal Rule"><div id="inserthorizontalrule" class="rtebtn1"><img src="' + rteImagePath + 'hr.gif"></div></td>');
        document.write('        <td width="5"><img src="' + rteImagePath + 'finish.gif" width="5" height="25"></td>');
        document.write('		<td width="100%"></td>');
        document.write('      </tr>');
        document.write('    </table></td>');
        document.write('  </tr>');
        document.write('  <tr>');
        document.write('    <td style="border:1px solid #C3DAF9; ">');
        document.write('		<iframe name="' + rteName + '" id="' + rteName + '" style="width:100%; height:' + rteHeight + 'px; background-color:#FFFFFF;" frameborder="0"></iframe>');
        document.write('		<textarea name="' + rteFormName + '" id="' + rteFormName + '" style="display:none;width:' + rteWidth + '; height:' + rteHeight + 'px; background-color:#FFFFFF; font-family:courier new; font-size:12px; color:#000000; border:0px;"></textarea>');
        document.write('		<iframe id="preview_' + rteName + '" style="width:' + rteWidth + '; height:' + rteHeight + 'px; background-color:#FFFFFF; display:none;" frameborder="0"></iframe>');
        document.write('	</td>');
        document.write('  </tr>');
        document.write('  <tr>');
        document.write('    <td bgcolor="#C3DAF9" height="25" class="rtebg" align="right" style="padding-right:3px" onmousedown="return false;">');
        document.write('            <a class="saveTextButton" onclick="returnRezulText();">&nbsp;'+saveBtnText+'&nbsp;</a> <a class="saveTextButton" onclick="closeWithOutRezulText();">&nbsp;'+cancelBtnText+'&nbsp;</a>');
        document.write('    </td>');
        document.write('  </tr>');
        document.write('</table>');
        startRTE(rtePreloadContent);
        menuBuilder();
    }
    $("#" + rteName).contents().keyup(function(e){ checkInputTextSize(); });
    $("#text-editor").css({ display: 'none' });
};
function checkInputTextSize(){
//    $("#sizerText").html($("#" + rteName).contents().find('body').html());
//    if(($("#sizerText").height() > rteMaxHeight)) {
//        $("#" + rteName).contents().find('body').html(oldContentRte);
//        alert("Введений текст перевищує розміри листівки");
//    } else {
//        oldContentRte = $("#" + rteName).contents().find('body').html();
//    }
}