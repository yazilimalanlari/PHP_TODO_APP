import Api from './api.js';

HTMLFormElement.prototype.getData = function () {
    const formData = new FormData(this);
    return Object.fromEntries(formData);
}

document.addEventListener('DOMContentLoaded', function () {
    /**
     * @type {HTMLFormElement}
     */
    const form = document.forms.item(0);

    form.addEventListener('submit', e => e.preventDefault() || (e.target.name === 'register' ? register() : login()));

    /**
     * @param {HTMLElement} parent 
     * @param {String} error 
     */
    function setError(parent, error) {
        const element = parent.querySelector('.error') ?? document.createElement('span');
        element.classList.add('error');
        element.innerText = error;
        if (element.parentNode == null) parent.appendChild(element);
    }

    function formElementValidate(element) {
        if (!element.checkValidity()) {
            setError(element.parentNode, element.validationMessage);
        }
    }

    function formValidate() {
        form.removeAttribute('data-nocustom-validate');
        if (form.checkValidity()) return true;
        for (const element of form.elements) {
            if (element.tagName !== 'INPUT') continue;
            formElementValidate(element);
        }
    }
    
    for (const element of form.elements) {
        if (element.tagName !== 'INPUT') break;
        element.addEventListener('input', () => {
            if (!form.hasAttribute('data-nocustom-validate')) {
                formElementValidate(element);
            }
        });
    }
    
    const redirect = (path = '/', timeout = 1000) => setTimeout(() => location.href = path, timeout);
    const showBoxMessage = (type, message) => {
        const boxMessage = document.querySelector('.box-message');
        boxMessage.innerText = message;
        boxMessage.setAttribute('data-type', type);
    }

    function register() {
        if (!formValidate()) return;
        Api.post('/entry/register', form.getData())
            .then(() => {
                showBoxMessage('success', 'Registration successful, you will be directed...');
                redirect('/login', 2000);
            })
            .catch(e => {
                showBoxMessage('error', 'Registration failed, please try again later!');
            });
    }

    function login() {
        if (!formValidate()) return;
        Api.post('/entry/login', form.getData())
            .then(() => {
                showBoxMessage('success', 'Login successful, You are redirected...');
                redirect();
            })
            .catch(e => {
                showBoxMessage('error', 'Login failed!');
            });
    }
});