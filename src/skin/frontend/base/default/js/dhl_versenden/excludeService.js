
function observeAndDisable(location, neighbour) {
    addKeypressListener(location, neighbour);
    addKeypressListener(neighbour, location);
}

function addKeypressListener(elem, sibling) {
    var orgPlaceholder = sibling.placeholder;
    elem.addEventListener('change', function(){
        self = elem;
        if (self.value !== '') {
            if (!sibling.disabled) {
                sibling.placeholder = '--------';
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



