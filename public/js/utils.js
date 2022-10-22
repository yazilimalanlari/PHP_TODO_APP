const listener = (event, element, cb) => element.addEventListener(event, e => cb(e, element));

NodeList.prototype.listener = function (event, /** @type {CallableFunction} */ cb) {
    this.forEach(element => listener(event, element, cb));
}

HTMLElement.prototype.listener = function (event, cb) {
    listener(event, this, cb);
}

HTMLElement.prototype.findParents = function (tagName) {
    let parent;
    while ((parent = (parent ? parent.parentNode : this.parentNode)) != null)
        if (parent.tagName === tagName.toUpperCase()) return parent;
    return null;
}

function $(selector) {
    const elements = document.querySelectorAll(selector);
    if (elements.length === 1) return elements[0];
    return elements;
}

function modal(title, message, yes) {
    const element = $('.modal');
    element.querySelector('.title').innerText = title;
    element.querySelector('.content').innerText = message;
    element.classList.add('show');

    element.querySelector('#yes').onclick = () => {
        yes();
        element.classList.remove('show');
    };
    element.querySelector('#cancel').onclick = () => element.classList.remove('show');
}

function dynamicListener(selector, event, cb) {
    document.addEventListener(event, e => {
        let element;
        if (element = e.composedPath().find(i => i.matches?.(selector)))
            return cb(e, element);
    });
}