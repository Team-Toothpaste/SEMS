// need to get data from database using ajax?
const accountTemplate = document.createElement('template');
accountTemplate.innerHTML = `
    <h1>Account</h1>
    <h4>Username: <a class="usernametag"></a></h4>
    <br/>
    <form class="usernameForm" name="usernameForm" action="">
        <label for="changeUsername">Change username</label>
        <input type="text" class="changeUsername" name="changeUsername"/>
        <input class="submitUsername" type="submit" value="submit"/>
    </form>
    <h4>Email: <a class="emailtag"></a></h4>
    <form name="emailForm" action="displayEmail()">
        <label for="changeEmail">Change email</label>
        <input type="text" id="changeEmail">
        <input type="submit" value="submit"/>
    </form>
    <h4>Password: </h4>
    <p><a href="../password-reset/index.html">Reset password</a></p>
`;
const profileTemplate = document.createElement('template');
profileTemplate.innerHTML = `
    <h1>Profile</h1>
    <h4>Profile image: <img id="profileImg"/></h4>
    <form name="imgForm" action="">
        <label for="changeImg">Change profile image:</label>
        <input type="file" name="imgUpload" id="imgUpload">
        <input type="submit" value="Upload image" name="submitImg">
    </form>
    <h4>Username: <a class="usernametag"></a></h4>
    <form class="usernameForm" name="usernameForm action="">
        <label for="changeName">Change username</label>
        <input type="text" class="changeUsername" name="changeUsername"/>
        <input class="submitUsername" type="submit" value="submit"/>
    </form>
`;
const securityTemplate = document.createElement('template');
securityTemplate.innerHTML = `
    <h1>Security</h1>
    <h4>Make my account private</h4>
`;
class AccountPopper extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.shadowRoot.appendChild(accountTemplate.content.cloneNode(true));
    }

    getDetails() {
        // ajax reques
        // get username 
    }
};
class ProfilePopper extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.shadowRoot.appendChild(profileTemplate.content.cloneNode(true));
    }

    getDetails() {
        // ajax reques
        // get username 
    }
};
class SecurityPopper extends HTMLElement {
    constructor() {
        super();
        this.attachShadow({mode: 'open'});
        this.shadowRoot.appendChild(securityTemplate.content.cloneNode(true));
    }
};
window.customElements.define('account-popper', AccountPopper);
window.customElements.define('profile-popper', ProfilePopper);
window.customElements.define('security-popper', SecurityPopper);