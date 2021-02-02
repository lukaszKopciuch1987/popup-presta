

document.addEventListener('click', function (event) {
    var alertElement = findUpTag(event.target, '.pc-alert');

    if (alertElement) {
        alertElement.style.display = 'none';
    }
});

function findUpTag(el, selector) {
    if (el.matches(selector)) {
        return el;
    }

    while (el.parentNode) {
        el = el.parentNode;
        if (el.matches && el.matches(selector)) {
            return el;
        }
    }
    return null;
}

$(".test").trigger("click");