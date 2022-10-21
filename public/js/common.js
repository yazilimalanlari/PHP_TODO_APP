import Api from './api.js';

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

document.addEventListener('DOMContentLoaded', function () {
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

    $('.delete').listener('click', (e, element) => {
        modal('Delete Task', 'Are you sure you want to delete the task?', () => {
            const taskElement = element.findParents('li');
            const id = taskElement.getAttribute('data-id');
            Api.delete(`/todo/${id}`).then(() => {
                taskElement.remove();
            }).catch((e) => {
                console.log(e);
            });
        });
    });

    $('.edit').listener('click', (e, element) => {
        const taskElement = element.findParents('li');
        const id = taskElement.getAttribute('data-id');
        const contentElement = taskElement.querySelector('.task-content');

        if (element.hasAttribute('data-edit-mode')) {
            alert()
            return;
        }

        element.setAttribute('data-edit-mode', true);        
        contentElement.setAttribute('contenteditable', true);
        contentElement.focus();
        getSelection().selectAllChildren(contentElement);
        
        let defaultContent = contentElement.innerText;
        
        const icon = element.querySelector('img');
        icon.src = '/images/icons/done.svg';

        window.onkeydown = (e) => {
            if (e.key !== 'Escape') return;
            contentElement.removeAttribute('contenteditable');
            contentElement.innerText = defaultContent;
            icon.src = '/images/icons/edit.svg';
            window.onkeydown = () => {};
        }
    });
});