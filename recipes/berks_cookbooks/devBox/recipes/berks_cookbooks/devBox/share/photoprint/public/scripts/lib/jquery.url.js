// JQuery URL Parser plugin - https://github.com/allmarkedup/jQuery-URL-Parser
// Written by Mark Perkins, mark@allmarkedup.com
// License: http://unlicense.org/ (i.e. do what you want with it!)

;
(function($, undefined) {

    var currentState = 1;
    var currentUrlInstanse = null;
    var currentUrl = "";
    var tag2attr = {
        a       : 'href',
        img     : 'src',
        form    : 'action',
        base    : 'href',
        script  : 'src',
        iframe  : 'src',
        link    : 'href'
    },

    key = ["source","protocol","authority","userInfo","user","password","host","port","relative","path","directory","file","query","fragment"], // keys available to query

    aliases = {
        "anchor" : "fragment"
    }, // aliases for backwards compatability

    parser = {
        strict  : /^(?:([^:\/?#]+):)?(?:\/\/((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?))?((((?:[^?#\/]*\/)*)([^?#]*))(?:\?([^#]*))?(?:#(.*))?)/,  //less intuitive, more accurate to the specs
        loose   :  /^(?:(?![^:@]+:[^:@\/]*@)([^:\/?#.]+):)?(?:\/\/)?((?:(([^:@]*):?([^:@]*))?@)?([^:\/?#]*)(?::(\d*))?)(((\/(?:[^?#](?![^?#\/]*\.[^?#\/.]+(?:[?#]|$)))*\/?)?([^?#\/]*))(?:\?([^#]*))?(?:#(.*))?)/ // more intuitive, fails on relative paths and deviates from specs
    },

    querystring_parser = /(?:^|&|;)([^&=;]*)=?([^&;]*)/g, // supports both ampersand and semicolon-delimted query string key/value pairs

    fragment_parser = /(?:^|&|;)([^&=;]*)=?([^&;]*)/g; // supports both ampersand and semicolon-delimted fragment key/value pairs

    function parseUri( url, strictMode )
    {
        var str = decodeURI( url ),
        res   = parser[ strictMode || false ? "strict" : "loose" ].exec( str ),
        uri = {
            attr : {},
            param : {},
            seg : {}
        },
        i   = 14;

        while ( i-- )
        {
            uri.attr[ key[i] ] = res[i] || "";
        }

        // build query and fragment parameters

        uri.param['query'] = {};
        uri.param['fragment'] = {};

        uri.attr['query'].replace( querystring_parser, function ( $0, $1, $2 ){
            if ($1)
            {
                uri.param['query'][$1] = $2;
            }
        });

        uri.attr['fragment'].replace( fragment_parser, function ( $0, $1, $2 ){
            if ($1)
            {
                uri.param['fragment'][$1] = $2;
            }
        });

        // split path and fragement into segments

        uri.seg['path'] = uri.attr.path.replace(/^\/+|\/+$/g,'').split('/');

        uri.seg['fragment'] = uri.attr.fragment.replace(/^\/+|\/+$/g,'').split('/');

        // compile a 'base' domain attribute

        uri.attr['base'] = uri.attr.host ? uri.attr.protocol+"://"+uri.attr.host + (uri.attr.port ? ":"+uri.attr.port : '') : '';

        return uri;
    };

    function getAttrName( elm )
    {
        var tn = elm.tagName;
        if ( tn !== undefined ) return tag2attr[tn.toLowerCase()];
        return tn;
    }

    $.fn.url = function( strictMode )
    {
        var url = '';

        if ( this.length )
        {
            url = $(this).attr( getAttrName(this[0]) ) || '';
        }

        return $.url({
            url : url,
            strict : strictMode
        });
    };

    $.url = function( opts )
    {
        var url     = '',
        strict  = false;

        if ( typeof opts === 'string' )
        {
            url = opts;
        }
        else
        {
            opts = opts || {};
            strict = opts.strict || strict;
            url = opts.url === undefined ? window.location.toString() : opts.url;
        }

        return {

            data : parseUri(url, strict),

            // get various attributes from the URI
            attr : function( attr )
            {
                attr = aliases[attr] || attr;
                return attr !== undefined ? this.data.attr[attr] : this.data.attr;
            },

            // return query string parameters
            param : function( param )
            {
                return param !== undefined ? this.data.param.query[param] : this.data.param.query;
            },


            // update url parameter
            push : function( param, value )
            {
                this.data.param.query[param] = value;
            },

            // update url parameter
            remove : function( param )
            {
                delete this.data.param.query[param];
            },

            // update url with parameters
            update : function( param, value)
            {
                var currentParams = "";
                for (var keyVar in this.data.param.query ) {
                    if(currentParams == "") {
                        currentParams += keyVar + "=" + this.data.param.query[keyVar];
                    } else {
                        currentParams += "&" + keyVar + "=" + this.data.param.query[keyVar];
                    }
                }
                currentParams = (currentParams == "") ? currentUrl : "?" + currentParams;
                try {
                    window.history.pushState({
                        state:currentState
                    }, "State " + currentState, currentParams);
                } catch(e){
                //$(window).location.hash = "123";
                }
            },

            // return fragment parameters
            fparam : function( param )
            {
                return param !== undefined ? this.data.param.fragment[param] : this.data.param.fragment;
            },

            // return path segments
            segment : function( seg )
            {
                if ( seg === undefined )
                {
                    return this.data.seg.path;
                }
                else
                {
                    seg = seg < 0 ? this.data.seg.path.length + seg : seg - 1; // negative segments count from the end
                    return this.data.seg.path[seg];
                }
            },

            // return fragment segments
            fsegment : function( seg )
            {
                if ( seg === undefined )
                {
                    return this.data.seg.fragment;
                }
                else
                {
                    seg = seg < 0 ? this.data.seg.fragment.length + seg : seg - 1; // negative segments count from the end
                    return this.data.seg.fragment[seg];
                }
            }

        };

    };

    $.currentUrl = function( ){
        if(currentUrlInstanse == null){
            currentUrlInstanse = $.url();
            currentUrl = currentUrlInstanse.attr('protocol') + "://" + currentUrlInstanse.attr('host') + currentUrlInstanse.attr('path');
        }
        return currentUrlInstanse;
    };

})(jQuery);