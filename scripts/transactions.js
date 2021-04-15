class TransactionsList extends HTMLElement {

    constructor() {
        super();
        this.shadowroot = this.attachShadow({mode: 'open'});
        this.data = null;
        this.rendered = false;
    }

    connectedCallback(){
        if(!this.rendered) this.render();
    }

    async load(){
        this.data = await new Promise((resolve, reject) => {
            $.ajax({
                type: 'GET',
                url: '/scripts/get-items.php',
                complete: xhr => {
                    switch(xhr.status){
                        case 200:
                            resolve(xhr.responseJSON.data)
                            break;
                        default:
                            reject(xhr.responseText);
                    }
                    const loadEvent = new CustomEvent('load');
                    this.dispatchEvent(loadEvent);
                }
            });
        });
    }

    async render(){
        await this.load();
        this.shadowroot.innerHTML = `<div id="content"></div>`;
        let content = this.shadowroot.querySelector('#content');
        let lastDate = "";
        $(content).append(`
            <style>
                
                :host, div#content {
                    display: flex;
                    width: 100%;
                    height: auto;
                    justify-content: flex-start;
                    align-items: center;
                    flex-direction: column;
                }
                
                div#content h3 {
                    align-self: flex-start;
                }
                
                div#content div.item {
                    display: flex;
                    flex-direction: row;
                    justify-content: flex-start;
                    align-items: center;
                    width: 100%;
                    min-height: 5vh;
                    box-sizing: border-box;
                    border-bottom: 2px solid var(--light-gray, lightgray);
                }
                
                div#content div.item p {
                    display: inline-flex;
                    flex-shrink: 1;
                    width: 100%;
                }
                
                div#content div.item p.item-title {
                    flex-direction: column;
                }
                
                div#content div.item p.item-title span:first-of-type {
                    font-weight: bold;
                }
                
                div#content div.item p.item-price {
                    text-align: right;
                    justify-content: flex-end;
                }
                
            </style>
        `);
        for(let item of this.data.items){
            if(item.timeAdded !== lastDate){
                $(content).append(`
                    <h3>${item.timeAdded}</h3>
                `);
                lastDate = item.timeAdded;
            }
            $(content).append(`
                <div class="item">
                    <p class="item-title"><span>${item.itemName}</span><span>${item.itemDesc}</span></p>
                    <p class="item-price">&pound;${item.itemPrice}</p>
                </div>
            `);
        }
        this.rendered = true;
        const renderEvent = new CustomEvent('render');
        this.dispatchEvent(renderEvent);
    }

}

customElements.define('transactions-list', TransactionsList);