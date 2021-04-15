class Cookie {
    constructor(n, v, e=1){
        if(this.isSet('sems-allow-cookies')){
            if(n !== undefined && v !== undefined && e !== undefined && !isNaN(e)){
                this.set(n, v, e);
            }
        }
        else{
            this.request(function(n, v, e){
                if(n !== undefined && v !== undefined && e !== undefined && !isNaN(e)){
                    this.set(n, v, e);
                }
            });
        }
        if(document.cookiesRequested === undefined){
            document.cookiesRequested = false;
        }
        else if(this.isSet('sems-allow-cookies')){
            document.cookiesRequested = true;
        }
    }

    set(name, value, e=1, userPreferenceOverride=false){
        var set = false;
        if(this.isSet('sems-allow-cookies') || userPreferenceOverride){
            if(name !== undefined && name != '' && value !== undefined && value != '' && e !== undefined && !isNaN(e)){
                var d = new Date();
                d = new Date(d.getTime() + (e*86400000));
                if(window.location.protocol.toLowerCase() == 'https:'){
                    document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';domain=' + window.location.hostname + ';path=/;secure;';
                    set = true;
                }
                else{
                    document.cookie = name + '=' + value + ';expires=' + d.toUTCString() + ';domain=' + window.location.hostname + ';path=/;';
                    set = true;
                }
            }
            else{
                throw "Cookie could not be set.";
            }
        }
        else{
            throw "Cookie could not be set: User not accepted cookies.";
        }
        return set;
    }

    get(name){
        if(name !== ''){
            var ca = decodeURIComponent(document.cookie).split(';'), c;
            for(var i = 0; i < ca.length; i++){
                c = ca[i];
                while(c.charAt(0) == ' '){
                    c = c.substring(1);
                }
                if(c.indexOf(name + '=') == 0){
                    return c.substring((name.length + 1), c.length);
                }
            }
            return undefined;
        }
        else{
            throw "Could not get cookie: Cookie name undefined";
        }
    }

    delete(name){
        if(this.isSet(name)){
            this.set(name, this.get(name), -10, true);
        }
    }

    isSet(name){
        const c = this.get(name);
        return (c !== undefined);
    }

    request(){
        if(!this.isSet('sems-allow-cookies') && !document.cookiesRequested){
            document.cookiesRequested = true;
            var n = confirm("Allow cookies on SEMS?");
            if(n === true){
                this.set('sems-allow-cookies', 'true', 1, true);
                const acceptEvent = new CustomEvent('cookies-accepted')
                window.dispatchEvent(acceptEvent);
            }
        }
    }
}

(new Cookie()).request();