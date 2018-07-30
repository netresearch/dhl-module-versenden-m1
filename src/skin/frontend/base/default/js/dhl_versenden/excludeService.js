/**
 * When setting a value on one of the input elements, the other will be disabled.
 *
 * @param {Element} location
 * @param {Element} neighbour
 */
function observeAndDisable(location, neighbour) {
    addKeypressListener(location, neighbour, Translator.translate('Not available with preferred location'));
    addKeypressListener(neighbour, location, Translator.translate('Not available with preferred neighbor'));
}

/**
 *
 * @param {Element} elem
 * @param {Element} sibling
 * @param {string} placeholder
 */
function addKeypressListener(elem, sibling, placeholder) {
    var orgPlaceholder = sibling.placeholder;
    elem.addEventListener('input', function(){
        self = elem;
        if (self.value !== '') {
            if (!sibling.disabled) {
                sibling.placeholder = placeholder;
                sibling.disable();
            }
        }
        if (self.value === '') {
            if (sibling.disabled) {
                sibling.placeholder = orgPlaceholder;
                sibling.enable();
            }
        }
    });
}



