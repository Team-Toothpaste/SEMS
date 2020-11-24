class TopMenu extends HTMLElement {
    constructor(){
        super();
        this.shadowroot = this.attachShadow({mode: 'open'});
        this.load();
    }
    
    load(){
        var x, o = this;
        if(window.XMLHttpRequest){
            x = new XMLHttpRequest();
        }
        else{
            x = new ActiveXObject("Microsoft.XMLHTTP");
        }
        x.onreadystatechange = function(){
            if(this.readyState == 4 && this.status == 200){
                o.shadowroot.innerHTML = this.responseText;
                document.getElementById('content').style.marginTop = "50px";
            }
        };
        x.open("GET", "http://brookes-sems.epizy.com/matt-register/scripts/dependencies/top-menu.html", true);
        x.send();
    }
}

customElements.define('menu-bar', TopMenu);