//Dark / light mode toggle

var isDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
const c = new Cookie();

window.toggleDarkMode = () => {
    const stylesheets = document.styleSheets;
    for(let stylesheet of stylesheets){
        for(let rule of Array.from(stylesheet.rules).reverse()){
            var mediaRule = rule.media;
            if(mediaRule === undefined) continue;
            if(mediaRule.mediaText.includes('prefers-color-scheme')){
                if(isDark){
                    //Change to light
                    mediaRule.appendMedium("original-prefers-color-scheme");
                    if(mediaRule.mediaText.includes('light')) mediaRule.deleteMedium("only screen and (prefers-color-scheme: light)");
                    if(mediaRule.mediaText.includes('dark')) mediaRule.deleteMedium("only screen and (prefers-color-scheme: dark)");
                    isDark = false;
                    c.set('sems-dark-mode', 'false');
                }
                else{
                    //Change to dark
                    mediaRule.appendMedium("only screen and (prefers-color-scheme: light)");
                    mediaRule.appendMedium("only screen and (prefers-color-scheme: dark)");
                    if (mediaRule.mediaText.includes("original")) mediaRule.deleteMedium("original-prefers-color-scheme");
                    isDark = true;
                    c.set('sems-dark-mode', 'true');
                }
            }
        }
    }
};

if(c.isSet('sems-dark-mode') && isDark != (c.get('sems-dark-mode') == 'true')) window.toggleDarkMode();

//Still change dark mode with system change and assume user override preset
window.matchMedia('only screen and (prefers-color-scheme: dark)').addEventListener('change', e => {
    if(c.isSet('sems-dark-mode')){
        if(e.matches != isDark) window.toggleDarkMode();
    }
});