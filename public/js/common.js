import Api from './api.js';

class Common {
    list = $('.tasks > ul');
    tempItems = [];
    iconPaths = {
        edit: '/images/icons/edit.svg',
        // delete: '/images/icons/delete.svg',
        done: '/images/icons/done.svg'
    };
    tasksMessageElement = $('#tasks-message');
    listeners = {};
    totalCount = 0;
    perPage = 0;
    
    constructor(totalCount, perPage) {
        this.totalCount = totalCount;
        this.perPage = perPage;
    }

    editTask(event, element) {
        const taskElement = element.findParents('li');
        const id = taskElement.getAttribute('data-id');
        const contentElement = taskElement.querySelector('.task-content');
        const defaultContent = contentElement.innerText;
        const icon = element.querySelector('img');

        if (id == null) return;

        const $default = () => {
            icon.src = this.iconPaths.edit;
            element.removeAttribute('data-edit-mode');
            contentElement.contentEditable = false;
        }

        if (element.hasAttribute('data-edit-mode') && !this.updateTask(id, contentElement, $default)) return;

        element.setAttribute('data-edit-mode', true);
        contentElement.setAttribute('data-default-content', defaultContent);
        contentElement.setAttribute('contenteditable', true);
        contentElement.focus();
        getSelection().selectAllChildren(contentElement);
                
        icon.src = this.iconPaths.done;

        window.addEventListener('keydown', (e) => {
            if (e.key !== 'Escape') return;
            contentElement.innerText = defaultContent;
            $default();
            window.onkeydown = () => {};
        });
    }

    updateTask(id, contentElement, $default) {
        const content = contentElement.innerText;

        if (content.trim().length === 0) {
            $default();
            const attrDefaultContent = contentElement.getAttribute('data-default-content');
            contentElement.removeAttribute('data-default-content');
            contentElement.innerText = attrDefaultContent;
            return;
        }
        
        Api.put(`/todo/${id}`, { content }).then(() => {}).catch(e => {
            alert('There is a problem!');
            contentElement.innerText = defaultContent;
        }).finally($default);
    }

    addTask(event) {
        event.target.disabled = true;
        const li = document.createElement('li');
        const taskElement = document.createElement('span');
        taskElement.classList.add('task-content');
        taskElement.contentEditable = true;
        taskElement.innerText = 'Write task...';
        li.setAttribute('data-save', false);
        li.append(taskElement);

        const actions = document.createElement('div');
        const saveElement = document.createElement('span');
        const saveIcon = document.createElement('img');

        actions.className = 'actions';
        saveElement.className = 'edit';
        saveIcon.src = this.iconPaths.done;
        saveIcon.alt = 'Done icon';

        saveElement.appendChild(saveIcon);
        actions.appendChild(saveElement);
        li.appendChild(actions);

        this.list.prepend(li);
        taskElement.focus();
        getSelection().selectAllChildren(taskElement);

        const params = { li, taskElement, event, actions, saveIcon };
        this.listeners.saveTask = {
            element: saveElement,
            listener: () => this.saveTask.call(this, params)
        };
        saveElement.addEventListener('mousedown', this.listeners.saveTask.listener);
        taskElement.addEventListener('focusout', () => this.addTaskCancel.call(this, params));

        if (this.list.children.length > 10) {
            const lastChildElement = this.list.lastElementChild;
            lastChildElement.remove();
            this.tempItems.push(lastChildElement);
        }
    }

    saveTask({ li, taskElement, event, actions, saveIcon }) {
        const content = taskElement.innerText.trim();
        if (content.length === 0) return;
        li.removeAttribute('data-save');
        Api.post('/todo', { content }).then(result => {
            taskElement.contentEditable = false;
            li.setAttribute('data-id', result.data.id);
            saveIcon.src = this.iconPaths.edit;
            saveIcon.alt = 'Edit icon';

            const deleteElement = document.createElement('span');
            deleteElement.className = 'delete';
            deleteElement.innerHTML = '<img src="/images/icons/delete.svg" alt="Delete icon" />';
            actions.appendChild(deleteElement);
            event.target.disabled = false;

            if (++this.totalCount === 1) this.tasksMessageElement.innerText = '';
            this.paginationConfig();
        }).catch(e => {
            console.log(e);
            alert('There is a problem!');
        }).finally(() => {
            const { element, listener } = this.listeners.saveTask;
            element.removeEventListener('mousedown', listener)
        });
    }

    addTaskCancel({ li, taskElement, event }) {
        if (taskElement.innerText.trim().length === 0 || li.hasAttribute('data-save')) {
            li.remove();
            event.target.disabled = false;
        }
    }

    deleteTask(element) {
        const taskElement = element.findParents('li');
        const id = taskElement.getAttribute('data-id');

        const tempReload = () => {
            if (this.tempItems.length > 0)
                this.list.appendChild(this.tempItems.pop());
        }

        if (id == null) {
            taskElement.remove();
            tempReload();
        } else {
            Api.delete(`/todo/${id}`).then(() => {
                taskElement.remove();
                tempReload();
                if (--this.totalCount === 0) {
                    this.tasksMessageElement.innerText = 'No saved tasks.'
                }
                this.paginationConfig();
            }).catch((e) => {
                console.log(e);
                alert('There is a problem!');
            });
        }
    }
    
    setComplete(event, element) {
        if (event.target !== element) return;
        Api.put(`/todo/complete/${element.getAttribute('data-id')}`, { status: element.classList.contains('completed') ? 0 : 1 })
            .then(() => {
                element.classList.toggle('completed');
            });
    }
    
    paginationConfig() {
        const pagination = $('.tasks .pagination');
        const totalPage = Math.ceil(this.totalCount / this.perPage);

        if (pagination.children.length > totalPage) {
            pagination.lastElementChild.remove();
            return this.paginationConfig();
        }

        for (let i = pagination.children.length; i < totalPage; i++) {
            const a = document.createElement('a');
            a.href = `/?page=${i + 1}`;
            a.innerText = i + 1;
            pagination.appendChild(a);
        }
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const tasksElement = $('.tasks[data-total-count]');
    const totalCount = tasksElement.getAttribute('data-total-count')
    const perPage = tasksElement.getAttribute('data-per-page')
    const common = new Common(Number(totalCount), Number(perPage));

    dynamicListener('.tasks .task-content', 'keydown', e => e.key === 'Enter' && e.preventDefault());
    dynamicListener('.tasks .delete', 'click', (e, element) => {
        modal('Delete Task', 'Are you sure you want to delete the task?', () => common.deleteTask(element));
    });
    dynamicListener('.tasks .edit', 'click', common.editTask.bind(common));
    dynamicListener('.tasks ul li', 'dblclick', common.setComplete.bind(common));
    $('#add-task').listener('click', common.addTask.bind(common));
});