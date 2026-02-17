/**
 * When setting a value on one of the input elements, the other will be disabled.
 *
 * @param {Element} location
 * @param {Element} neighbour
 */
function observeAndDisable(location, neighbour) {
    addKeypressListener(location, neighbour, Translator.translate('Not available with drop-off location'));
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

/**
 * Mutual exclusion between Preferred Neighbour and No Neighbour Delivery checkboxes.
 *
 * When one is checked, the other gets unchecked and disabled.
 * Checking NND also disables the PN text detail input.
 *
 * @param {Element} pnCheckbox    - Preferred Neighbour checkbox
 * @param {Element} nndCheckbox   - No Neighbour Delivery checkbox
 * @param {Element} pnDetailInput - Preferred Neighbour text detail input
 */
function observeCheckboxExclusion(pnCheckbox, nndCheckbox, pnDetailInput) {
    pnCheckbox.addEventListener('change', function () {
        if (pnCheckbox.checked) {
            nndCheckbox.checked = false;
            nndCheckbox.disable();
        } else {
            nndCheckbox.enable();
        }
    });

    nndCheckbox.addEventListener('change', function () {
        if (nndCheckbox.checked) {
            pnCheckbox.checked = false;
            pnCheckbox.disable();
            if (pnDetailInput) {
                pnDetailInput.value = '';
                pnDetailInput.disable();
            }
        } else {
            pnCheckbox.enable();
            if (pnDetailInput) {
                pnDetailInput.enable();
            }
        }
    });

    // Enforce initial state in case a checkbox is already checked on page load
    if (pnCheckbox.checked) {
        nndCheckbox.disable();
    } else if (nndCheckbox.checked) {
        pnCheckbox.disable();
        if (pnDetailInput) {
            pnDetailInput.disable();
        }
    }
}

