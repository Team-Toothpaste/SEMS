class Request {
    
    constructor(data={}){
        if(!('type' in data && 'url' in data)) throw "Could not form request: No type or URL specified.";
        this.data = data;
        var x;
        if(window.XMLHttpRequest){
            x = new XMLHttpRequest();
        }
        else{
            x = new ActiveXObject("Microsoft.XMLHTTP");
        }
        x.onreadystatechange = function(){
            if(this.readyState == 4){
                if(this.status in [200, 201]){
                    if('success' in data && typeof data.success == 'function') data.success();
                }
                else{
                    if('error' in data && typeof data.error == 'function') data.error();
                }
                if('complete' in data) data.complete();
            }
        };
    }
    
    send(){
        x.send(('data' in this.data) ? this.data.data : null);
    }
    
}