
function observeAndDisable(location, neighbour) {
    addKeypressListener(location, neighbour);
    addKeypressListener(neighbour, location);
}

function addKeypressListener(elem, sibling) {
    elem.addEventListener('change', function(){
        self = elem;
        if (sibling.value !== '' && self.value !== '') {
            sibling.disable();
        }
        if(self.value === '') {
            if (sibling.disabled) {
                sibling.enable();
            }
        }
    });
}



